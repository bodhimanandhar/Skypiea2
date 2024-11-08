<html>
    <head>
    <link href="usertable.css" rel="stylesheet" type="text/css">
    </head>
<body style="background-color:white">
  <div class="form-container">
        <h2 class="form-title">Create New Account</h2>

        <form action="usertable_process.php" method="POST">
            <div class="input-wrapper">
                <label>First Name:</label>
                <input type="text" name="First_name" placeholder="John" required>
            </div>

            <div class="input-wrapper">
                <label>Last Name:</label>
                <input type="text" name="Last_name" placeholder="Doe" required>
            </div>

            <div class="input-wrapper">
                <label>Email:</label>
                <input type="text" name="Email" placeholder="John@gmail.com" required>
            </div>

            <div class="input-wrapper">
                <label>User Name:</label>
                <input type="text" name="User_name" placeholder="John123" required>
            </div>

            <div class="input-wrapper">
                <label>Password:</label>
                <input type="password" name="Password" placeholder="*****" required>
            </div>

            <input type="submit" value="Save">
        </form>

     
    </div>

</body>
</html>