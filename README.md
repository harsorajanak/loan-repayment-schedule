

## About Application

Loan Emi Repayment Schedule RestApis


## How to install 
    
    

    $> Composer Install

    Generate JWT SCRET using below command

    $> php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    
    $> php artisan jwt:secret

    JWT_SECRET= [past here you get using above command]
    LOAN_INTEREST=10
    LOAN_EMI_DAY=7
    
    write all SMTP information here 
    MAIL_MAILER=smtp
    MAIL_HOST=mailhog
    MAIL_PORT=1025
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS=null

    Write localhost database credential
    DB_CONNECTION=mysql
    DB_HOST=localhost
    DB_PORT=3306
    DB_DATABASE=db_practical
    DB_USERNAME=root
    DB_PASSWORD=
    
    $> php artisan migrate

    $> php artisan key:generate

    Lat step is to run the app

    $> php artisan serve
