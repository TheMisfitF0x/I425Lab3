<?php

?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <!-- TODO: Replace this CSS link with a series of CSS files that correctly influence the application -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- css for the signin page -->
    <link rel="stylesheet" href="css/index.css" >
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/warehouse.css">
    <link rel="stylesheet" href="css/order_preview.css">
    <link rel="stylesheet" href="css/product.css">
    <link rel="stylesheet" href="css/order.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/signin.css">
    <title>Warehouse SPA</title>

</head>
<body class="d-flex flex-column h-100">

<header>
    <!-- Fixed navbar -->
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#home" style="color:#FFFFFF; font-weight: bold"><img src="img/oddity_warehouse_logo.svg" style="width:40px;">&nbsp Oddity Warehouse</a>


            <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse collapse" id="navbarCollapse" style="">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item" id="li-warehouse">
                        <a class="nav-link disabled" href="#warehouse">Warehouses <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item" id="li-product">
                        <a class="nav-link disabled" href="#product">Products</a>
                    </li>
                    <li class="nav-item" id="li-order">
                        <a class="nav-link disabled" href="#order">Orders</a>
                    </li>
                    <li class="nav-item" id="li-admin">
                        <a class="nav-link disabled" href="#admin">Admin</a>
                    </li>
                    <li class="nav-item" id="li-signin">
                        <a class="nav-link" href="#signin">Sign in</a>
                    </li>
                    <li class="nav-item" id="li-signup" style="display: none;">
                        <a class="nav-link" href="#signup">Sign up</a>
                    </li>
                    <li class="nav-item" id="li-signout" style="display: none;">
                        <a class="nav-link" href="#signout">Sign out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
