<?php
// File: header.inc.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="description" content="Professional job application form for TechNova IT Careers">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? $page_title : 'TrioSoft IT Solutions'; ?></title>
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="icon" type="image/png" href="images/favicon.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div id="wb_header">
    <div id="header">
      <div class="row">
        <div class="col-1">
          <div
            id="wb_headerImage"
            style="
              display: inline-block;
              width: 119px;
              height: 54px;
              z-index: 0;
            "
          >
            <a href="./index.php">
            <img
              src="images/TrioSoft_white_small.png"
              id="headerImage"
              alt="This is a logo of TrioSoft IT Solutions"
              width="130"
              height="54"
            >
            </a>
          </div>
        </div>
        <div class="col-2">
          <div
            id="wb_headerMenu"
            style="display: block; width: 100%; z-index: 1"
          >
            <div
              id="headerMenu"
              class="headerMenu"
              style="width: 100%; height: auto !important"
            >
              <div class="container">
                <div class="navbar-header">
                </div>
                <div class="headerMenu-navbar-collapse collapse">
                  <ul class="nav navbar-nav">
                    <li class="nav-item">
                      <a
                        class="nav-link headerMenu-effect"
                        href="./index.php"
                        target="_self"
                        >Home</a
                      >
                    </li>
                    <li class="nav-item">
                      <a
                        class="nav-link headerMenu-effect"
                        href="./jobs.php"
                        target="_self"
                        >Jobs</a
                      >
                    </li>
                    <li class="nav-item">
                      <a
                        class="nav-link headerMenu-effect"
                        href="./apply.php"
                        target="_self"
                        >Job Apply</a
                      >
                    </li>
                    <li class="nav-item">
                      <a
                        class="nav-link headerMenu-effect"
                        href="./about.php"
                        target="_self"
                        >About Us</a
                      >
                    </li>
                    <li class="nav-item">
                      <a
                        class="nav-link headerMenu-effect"
                        href="./index.php#contact"
                        target="_self"
                        >Contact</a
                      >
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>