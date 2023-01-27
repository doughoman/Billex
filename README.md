# BillEX
[![N|Solid](http://hitesh.dev.billex.net/images/billex.png)](http://hitesh.dev.billex.net/)
# Getting Started
---
Follow the below instructions are for brief instructions to copy and run the project on server and links on where to ge for each api to signup for new credentials For product on, and any steps that need to be done to get a new server running.
# Installing
---
- clone the repesitory to server: https://bitbucket.org/doughoman/billex2/src/develop/
- Create public_html tor symlink
- Gve 777 permission to writable folder and it's sub folder and all files
# Run composer install
- Reference URL: https://getcomposer.org/doc/0o-Intro.md
# Setup.env file
- Copy env.example to env and set ervironmenr's values by removing:""#
# Setup 3rd party APIs
### 1) Amzon SES
#### Step 1
- Installation URL: https://github.com/aws/aws-sdk-php
- Reference URL: https://github.com/awsdocs/amazon-ses-developer-guide/blob/master/doc-source/send-using-sdk-php.md
#### Step 2
- Create Acccunt URL: https://portal.aws.amazon.com/billing/signup#/start
- Create your account and get credential like: Access Key and Secret Access Key
- Create SMTP account and get credential like: SMTP Username,SMTP Password and SMTP host
- Access Key : XXXXXXXXXXXXXXCJ
- Secret Access Key : XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXlB3
- SMTP Username : XXXXXXXXXXXXXXMNZ
- SMTP Password : XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXlLa
- SMTP host : XXXXXXXXXXXXXXXXXXXXXX.amazonaws.com
### 2) Amzon SNS
#### Step 1
- Installation URL: https://portal.aws.amazon.com/billing/signup#/start
- Reference URL: https://github.com/aws/aws-php-sns-message-validator
#### Step 2
- Create Acccunt URL: https://portal.aws.amazon.com/billing/signup
- Create your account and get credential like: Access Key and Secret Access Key
- Access Key : XXXXXXXXXXXXXXCJ
- Secret Access Key : XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXlB3
### 3) Login with Google Account
#### Step 1
- Go to the [Google API Console](https://console.developers.google.com/)
#### Step 2
- Select an existing project from the projects list, or click `NEW PROJECT` to create a new project:
- Enter the `Project Name`.
- Under the Project Name, you will see the Google API console automatically creates a project ID. Optionally you can change - this project ID by the `Edit` link. But project ID must be unique worldwide.
- Click on the `CREATE` button and the project will be created in some seconds.
#### Step 3
- In the left side navigation panel, select `Credentials` under the `APIs & Services` section.
#### Step 4
- Select the `OAuth consent screen` tab, specify the consent screen settings.
- In `Application name` field, enter the name of your Application.
- In `Support email` filed, choose an email address for user support.
- In the `Authorized domains`, specify the domains which will be allowed to authenticate using OAuth.
- Click the `Save` button.
#### Step 5
- Select the `Credentials` tab, click the `Create credentials` drop-down and select `OAuth client ID`.
- In the `Application type` section, select `Web application`.
- In the `Authorized redirect URIs` field, enter the redirect URL.
- Click the `Create` button.
### 4) Stripe
#### Step 1
- Create Acccunt URL: https://dashboard.stripe.com/register
- Create your account and get credential like: Publishable key,Secret key And client ID
- Publishable key: XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXBqr
- Secret key : XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXRrS
- client ID : XXXXXXXXXXXXXXXXXXXXXXXXXXXXbPF
#### Step 2
- Installation URL: https://stripe.com/docs/connect/standard-accounts
- Reference URL: https://stripe.com/docs/connect