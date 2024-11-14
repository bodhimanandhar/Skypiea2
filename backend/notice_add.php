<html>
<head>
    <link href="usertable.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="form-container">
    <a href="http://localhost/Skypiea2/frontend/notice.php" style="float:left"><i class="fas fa-arrow-left"></i></a>
    <form action="notice_process.php" method="POST">
        <h2 class="form-title">Add Notice</h2>

        <input type="hidden" name="user_id" value="<?php echo $id; ?>"/>
        <div class="input-wrapper">
            <label>Content</label>
            <textarea name="content"rows="4" cols="75"></textarea>
            <br/>
        </div>
     
        <div class="input-wrapper">
            <input type="submit" value="Save"/>
        </div>
    </form>
    </div>
</body>
</html>
