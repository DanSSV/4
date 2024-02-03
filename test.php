<!DOCTYPE html>
<html>

<head>
    <title>Insert Random Data</title>
</head>

<body>
    <button id="generateData">Generate and Store Random Data</button>
    <div id="result"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#generateData").click(function () {
                // Generate random data in JavaScript
                let Toda = generateRandomNumber(-90, 90).toFixed(8);
                let grade = generateRandomNumber(-180, 180).toFixed(8);
                let section = generateRandomString();
                let school = generateRandomString();
                let surname = generateRandomString();
                let nickname = generateRandomString();
                let middle = generateRandomString();
                let date = generateRandomDate();


                // Send data to PHP using AJAX
                $.ajax({
                    type: "POST",
                    url: "store_data.php",
                    data: {
                        name: name,
                        grade: grade,
                        section: section,
                        school: school,
                        surname: surname,
                        nickname: nickname,
                        middle: middle,
                        date: date,

                    },
                    dataType: "text",
                    success: function (response) {
                        $("#result").html(response);
                    }
                });
            });
        });

        // Function to generate random alphanumeric string
        function generateRandomString(length = 8) {
            let characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let randomString = '';
            for (let i = 0; i < length; i++) {
                randomString += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            return randomString;
        }

        // Function to generate random date
        function generateRandomDate() {
            let start = new Date(2020, 0, 1).getTime();
            let end = new Date(2023, 11, 31).getTime();
            let randomTimestamp = Math.random() * (end - start) + start;
            return new Date(randomTimestamp).toISOString().slice(0, 10);
        }

        // Function to generate random number within a range
        function generateRandomNumber(min, max) {
            return Math.random() * (max - min) + min;
        }
    </script>
</body>

</html>