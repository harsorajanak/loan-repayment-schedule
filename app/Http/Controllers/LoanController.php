<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanEmi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    public function loanRequest(Request $request){
        $requestData = $request->only('loan_amount', 'terms_week');

        //request params masks
        $rules = [
            'loan_amount' => 'required',
            'terms_week' => 'required|integer'
        ];

        //check request params
        $validator = Validator::make($requestData, $rules);
        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()]);
        }

        // get loged in user details
        $user = Auth::user();

        //count loan interest
        $loan_interest = $request->loan_amount * getenv('LOAN_INTEREST') / 100;

        //Loan request creating
        $create = Loan::create([
            "loan_amount" => $request->loan_amount,
            "terms_week" => $request->terms_week,
            "interest_amount" => $loan_interest,
            "user_id" => $user->id
        ]);

        return response()->json(['success'=> true, 'message'=> 'Load requested successfully','data'=>$create]);
    }

    public function approveLoan(Request $request){
        $requestData = $request->only('loan_id','status');

        //request params masks
        $rules = [
            'loan_id' => 'required|integer',
            'status' => 'required'
        ];

        //check request params
        $validator = Validator::make($requestData, $rules);
        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()]);
        }
        // get loan details
        $getLoanDetails = Loan::where('id',$request->loan_id)->first();

        //check loan request exists or not
        if($getLoanDetails && $getLoanDetails->status == "pending" && $request->status == "approved"){
            $loanEmiData = [];
            //Loan emi calculate logic
            for($i=1;$i<$getLoanDetails->terms_week+1;$i++){
                $day = $i * getenv('LOAN_EMI_DAY');
                $interest = round($getLoanDetails->interest_amount /  $getLoanDetails->terms_week,2);
                $totalLoanAmt = round(($getLoanDetails->loan_amount / $getLoanDetails->terms_week) + $interest,2);
                $emiDate = date('Y-m-d', strtotime("+$day days"));

                $loanEmi = [
                    "loan_id" => $request->loan_id,
                    "emi_amount" =>$totalLoanAmt,
                    "interest_amount"=>$interest,
                    "emi_date"=>$emiDate,
                    "created_at"=>date('Y-m-d h:i:s'),
                    "updated_at"=>date('Y-m-d h:i:s')
                ];

                $loanEmiData[] = $loanEmi;
            }
            // loan status update
            Loan::where('id',$request->loan_id)->update(['status'=>'approved']);
            //Loan emi create
            LoanEmi::insert($loanEmiData);

            return response()->json(['success'=> true, 'message'=> 'Load request approved successfully']);

        } else if($getLoanDetails && $getLoanDetails->status == "pending" && $request->status == "rejected") {

            Loan::where('id',$request->loan_id)->update(['status'=>'rejected']);

            return response()->json(['success'=> true, 'message'=> 'Load request rejected successfully']);
        } else {
            return response()->json(['success'=> false, 'error'=> 'Loan request not found!']);
        }
    }

    public function getLoanStatus(Request $request){
        $user = Auth::user();
        $getLoanStatus = Loan::where('user_id',$user->id)->with('loanEmi')->get();
        if(count($getLoanStatus) > 0){
            return response()->json(['success'=> true, 'message'=> 'Loan details found successfully','data'=>$getLoanStatus]);
        } else {
            return response()->json(['success'=> false, 'error'=> 'Loan request not found!']);
        }
    }

    public function repaymentLoan(Request $request){
        //get loan details
        $getLoanDetail = Loan::where('id',$request->loan_id)->first();

        // dynamic query logic
        $getLoanEmi = LoanEmi::query();
        $getLoanEmi->where('loan_id',$request->loan_id);
        $getLoanEmi->whereNull('emi_paid_date');

        // get total count of emi
        $totalCount = $getLoanEmi->count();

        // get recent emi detail
        $emiData = $getLoanEmi->orderBy('emi_date','ASC')->first();

        // check if last repayment and middle of repayment
        if($totalCount == 1) {
            LoanEmi::where('id',$emiData->id)->update(['emi_paid_date'=>date('Y-m-d')]);

            $balance = round($getLoanDetail->balance + $emiData->emi_amount,2);

            Loan::where('id',$request->loan_id)->update(['balance'=>$balance,'loan_completion_date'=>date('Y-m-d')]);

            return response()->json(['success'=> true, 'message'=> 'Repayment successfully, Congratulation your loan successfully completed']);

        } else if($totalCount > 1){
            LoanEmi::where('id',$emiData->id)->update(['emi_paid_date'=>date('Y-m-d')]);

            $balance = round($getLoanDetail->balance + $emiData->emi_amount,2);

            Loan::where('id',$request->loan_id)->update(['balance'=>$balance]);

            return response()->json(['success'=> true, 'message'=> 'Repayment successfully']);
        } else {
            return response()->json(['success'=> false, 'error'=> 'Requested loan status already completed!']);
        }
    }
}
