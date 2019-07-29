<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?= !empty($title) ? urldecode($title) : "Terms" ?></title>
    <meta version="13">

    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">

    <style>
      body {
        background-color: whites;
      }

      .main {
        text-align: center;
      }
    </style>
</head>
<body>
    <!-- $header Start -->

    <div class="main">
      <div class="home_live wow zoomIn" style="visibility: visible; animation-name: zoomIn;">
          <div><?= nl2br($item -> content) ?></div>
      </div>

      <style>
      </style>

      <script>
      </script>
    </div>
  </body>
</html>
