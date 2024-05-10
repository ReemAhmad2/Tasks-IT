<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تاسك جديد</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            direction: rtl;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #232323;
        }
        p {
            color: #3a3a3a;
        }
    </style>
</head>
<body>
    <div class="container">

        <h1>تاسك جديد</h1>
        <p>مرحبا عزيزي الطالب</p>
        <p>
            يسرني أن أخبرك قد تم نشر تاسك جديد في مادة
            <span style="color: red; font-style: italic;font-size: 18px">{{ $task->subject->name }}</span>
        </p>
        <p>
             من قبل المهندس/ة
            <span style="color: red; font-style: italic;font-size: 18px">{{ $task->teacher->user->name }}</span>
        </p>
        <p>الرجاء مراجعة الموقع لمعرفة المزيد من التفاصيل</p>


    </div>
</body>
</html>
