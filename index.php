<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        ExploreWorld - Tourist Recommendation System
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <!-- =====================================================
         LOGO STYLE
    ====================================================== -->

    <style>

        /* ================= LOGO ================= */

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }


        /* Round logo container */

        .logo-icon {
            width: 48px;
            height: 48px;

            border-radius: 50%;

            overflow: hidden;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #ffffff;

            border: 3px solid #20c997;

            box-shadow:
                0 3px 10px
                rgba(0, 0, 0, 0.20);

            flex-shrink: 0;
        }


        /* Logo image */

        .logo-icon img {
            width: 100%;
            height: 100%;

            object-fit: cover;

            display: block;
        }


        /* Logo text */

        .logo-text {
            color: #ffffff;
        }


        .logo-text span {
            color: #20c997;
        }


    </style>

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<header class="navbar">


    <!-- ================= LOGO ================= -->

    <a
        href="index.php"
        class="logo"
    >


        <span class="logo-icon">

            <img
                src="assets/images/travel-icon.png"
                alt="Travel"
            >

        </span>


        <span class="logo-text">

            Explore<span>World</span>

        </span>


    </a>


    <!-- ================= NAVIGATION ================= -->

    <ul class="nav-links">


        <!-- HOME -->

        <li>

            <a href="index.php">

                Home

            </a>

        </li>


        <!-- FEATURES -->

        <li>

            <a href="#features">

                Features

            </a>

        </li>


        <!-- USER LOGIN -->

        <li>

            <a href="auth/login.php">

                User Login

            </a>

        </li>


        <!-- REGISTER -->

        <li>

            <a href="auth/register.php">

                Register

            </a>

        </li>


        <!-- ADMIN -->

        <li>

            <a href="admin/login.php">

                Admin

            </a>

        </li>


    </ul>


</header>



<!-- =====================================================
     HERO SECTION
===================================================== -->

<section class="hero">


    <div class="hero-content">


        <h1>


            Discover Your Next


            <span>

                Adventure

            </span>


        </h1>


        <p>


            Explore beautiful tourist destinations,
            discover amazing experiences, read real
            traveller reviews and plan your perfect journey.


        </p>


        <div>


            <!-- ================= EXPLORE PLACES ================= -->


            <a
                href="places/index.php"
                class="btn btn-primary"
            >

                Explore Places

            </a>


            <!-- ================= JOIN US ================= -->


            <a
                href="auth/register.php"
                class="btn btn-outline"
            >

                Join Us

            </a>


        </div>


    </div>


</section>



<!-- =====================================================
     RUNNING INFORMATION
===================================================== -->

<div class="ticker">


    <div class="ticker-content">


        🌍 Discover Amazing Destinations


        &nbsp;&nbsp;&nbsp;


        ⭐ Read Traveller Reviews


        &nbsp;&nbsp;&nbsp;


        ❤️ Save Favourite Places


        &nbsp;&nbsp;&nbsp;


        📍 Find Places on Map


        &nbsp;&nbsp;&nbsp;


        ✈️ Plan Your Next Adventure


        &nbsp;&nbsp;&nbsp;


        🤖 Get Personalized Recommendations


        &nbsp;&nbsp;&nbsp;


    </div>


</div>



<!-- =====================================================
     POPULAR DESTINATIONS
===================================================== -->

<section class="section">


    <div class="section-title">


        <h2>

            Popular Destinations

        </h2>


        <p>


            Explore beautiful destinations
            and discover your next journey.


        </p>


    </div>



    <div class="cards">



        <!-- =================================================
             GOA
        ================================================= -->


        <div class="card">


            <img
                src="assets/images/goa.jpg"
                alt="Goa"
            >


            <div class="card-content">


                <h3>

                    Goa 🌴

                </h3>


                <p>


                    Enjoy beautiful beaches,
                    nightlife and unforgettable
                    travel experiences.


                </p>


            </div>


        </div>



        <!-- =================================================
             MANALI
        ================================================= -->


        <div class="card">


            <img
                src="assets/images/manali.jpg"
                alt="Manali"
            >


            <div class="card-content">


                <h3>

                    Manali 🏔️

                </h3>


                <p>


                    Experience mountains,
                    snow, nature and exciting
                    adventures.


                </p>


            </div>


        </div>



        <!-- =================================================
             KERALA
        ================================================= -->


        <div class="card">


            <img
                src="assets/images/kerala.jpg"
                alt="Kerala"
            >


            <div class="card-content">


                <h3>

                    Kerala 🌴

                </h3>


                <p>


                    Explore beautiful backwaters,
                    greenery, beaches and
                    local culture.


                </p>


            </div>


        </div>


    </div>


</section>



<!-- =====================================================
     FEATURES
===================================================== -->

<section
    class="section features"
    id="features"
>


    <div class="section-title">


        <h2>

            Everything You Need

        </h2>


        <p>


            Our platform gives travellers
            everything they need to discover
            and plan their journey.


        </p>


    </div>



    <div class="feature-grid">



        <!-- =================================================
             SEARCH
        ================================================= -->


        <div class="feature">


            <div class="feature-icon">

                🔎

            </div>


            <h3>

                Search Places

            </h3>


            <p>


                Search for tourist destinations
                quickly using our search bar.


            </p>


        </div>



        <!-- =================================================
             RECOMMENDATION
        ================================================= -->


        <div class="feature">


            <div class="feature-icon">

                🤖

            </div>


            <h3>

                Recommendations

            </h3>


            <p>


                Discover recommended places
                based on your interests,
                favourites and experiences.


            </p>


        </div>



        <!-- =================================================
             FAVOURITES
        ================================================= -->


        <div class="feature">


            <div class="feature-icon">

                ❤️

            </div>


            <h3>

                Favourite Places

            </h3>


            <p>


                Save places you love and
                easily find them again
                from your user account.


            </p>


        </div>



        <!-- =================================================
             REVIEWS
        ================================================= -->


        <div class="feature">


            <div class="feature-icon">

                ⭐

            </div>


            <h3>

                Public Reviews

            </h3>


            <p>


                Share your travel experience
                and read reviews posted by
                other travellers.


            </p>


        </div>



        <!-- =================================================
             MAP
        ================================================= -->


        <div class="feature">


            <div class="feature-icon">

                🗺️

            </div>


            <h3>

                Map Location

            </h3>


            <p>


                View tourist places on a map
                and open their location
                easily.


            </p>


        </div>



        <!-- =================================================
             USER ACCOUNT
        ================================================= -->


        <div class="feature">


            <div class="feature-icon">

                👤

            </div>


            <h3>

                User Account

            </h3>


            <p>


                Create your account, login
                securely and manage your
                travel activities.


            </p>


        </div>



        <!-- =================================================
             ADMIN
        ================================================= -->


        <div class="feature">


            <div class="feature-icon">

                🔐

            </div>


            <h3>

                Secure Admin Panel

            </h3>


            <p>


                Only authorized administrators
                can access the administration
                panel.


            </p>


        </div>



        <!-- =================================================
             DATABASE
        ================================================= -->


        <div class="feature">


            <div class="feature-icon">

                🔄

            </div>


            <h3>

                Automatic Updates

            </h3>


            <p>


                New registrations, reviews,
                experiences and other data
                are stored automatically.


            </p>


        </div>


    </div>


</section>



<!-- =====================================================
     HOW IT WORKS
===================================================== -->

<section class="section">


    <div class="section-title">


        <h2>

            How It Works

        </h2>


        <p>

            Finding your next destination is simple.

        </p>


    </div>



    <div class="cards">



        <!-- =================================================
             STEP 1
        ================================================= -->


        <div class="card">


            <div class="card-content">


                <h3>

                    01. Search 🔎

                </h3>


                <p>


                    Search for a tourist destination
                    that you want to explore.


                </p>


            </div>


        </div>



        <!-- =================================================
             STEP 2
        ================================================= -->


        <div class="card">


            <div class="card-content">


                <h3>

                    02. Explore 🌍

                </h3>


                <p>


                    Check images, information,
                    reviews, experiences and
                    location.


                </p>


            </div>


        </div>



        <!-- =================================================
             STEP 3
        ================================================= -->


        <div class="card">


            <div class="card-content">


                <h3>

                    03. Get Recommendation 🤖

                </h3>


                <p>


                    Discover other destinations
                    recommended according to
                    your interests.


                </p>


            </div>


        </div>


    </div>


</section>



<!-- =====================================================
     CALL TO ACTION
===================================================== -->

<section class="cta">


    <h2>

        Ready to Explore?

    </h2>


    <p>


        Create your account and start
        discovering amazing tourist
        destinations today.


    </p>


    <a
        href="auth/register.php"
        class="btn btn-primary"
    >

        Create Free Account

    </a>


</section>



<!-- =====================================================
     FOOTER
===================================================== -->

<footer>


    <p>


        © <?php echo date("Y"); ?>


        ExploreWorld -

        Tourist Recommendation System


    </p>


    <p>


        Discover • Explore • Experience


    </p>


</footer>



</body>

</html>