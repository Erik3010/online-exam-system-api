# Online Exam System API
## Installation

Clone this repository to your local machine

```
git clone git@github.com:Erik3010/online-exam-system-api.git
```

Switch to the repository folder
```
cd online-exam-system-api
```

Copy the `.env.example` file
```
cp .env.example .env
```

Create your own database and inform `.env`

Generate new application key
```
php artisan key:generate
```

Create the table and dummy data
```
php artisan migrate --seed
```

## Run the project

To run the project use this command:
```
php artisan serve
```