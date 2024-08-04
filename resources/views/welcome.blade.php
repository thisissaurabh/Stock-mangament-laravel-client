<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <style>
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');

        /* reset css */

        html {
            box-sizing: border-box;
            font: 16px/1.5 Georgia, 'Times New Roman', Times, serif;
        }

        *,
        *::before,
        *::after {
            box-sizing: inherit;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(90deg, #ffffff, rgba(241, 241, 241, 1));
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            gap: 6em;
        }

        /* header css */

        .site-header {
            padding-top: 1em;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            align-items: center;
        }

        .site-header p {
            color: #800080;
            font-size: 1.5em;
            font-weight: bold;
        }

        .site-header p::after {
            display: block;
            content: 'The last Web Agency you will ever need';
            color: #000;
            font-weight: normal;
            text-transform: uppercase;
            font-size: 1rem;
            letter-spacing: -0.03rem;
        }

        .site-navigation ul {
            display: flex;
            gap: 1.5em;
        }

        .site-navigation ul li {
            display: block;
            list-style: none;
        }

        .site-navigation ul li a {
            color: #000;
            text-decoration: none;
            font-size: 1.2em;
        }

        /* main css */

        .site-main {
            padding-bottom: 6em;
            box-shadow: 0 1em 1em 0 rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6em;
        }

        .site-main .main-header {
            max-width: 50%;
            display: flex;
            align-items: center;
        }

        .site-main .main-header article {
            flex: 1 1 60%;
            display: flex;
            flex-direction: column;
            gap: 1em;
        }

        .site-main .main-header article h1 {
            font-size: 1.5rem;
        }

        .site-main .main-header article a {
            align-self: flex-start;
            padding: 0.5em 1em;
            color: #000;
            text-decoration: none;
            border: 0.1em solid #000;
            border-radius: 0.3em;
            transition: box-shadow 0.4s ease-in-out;
        }

        .site-main .main-header article a:hover {
            box-shadow: 0 0 1em 0 rgba(0, 0, 0, 0.5);
        }

        .site-main .main-header svg {
            flex: 1 1 20%;
            font-size: 10em;
            scale: 1;
            transition: scale 0.4s ease-in-out;
        }

        .site-main .main-header svg:hover {
            scale: 1.1;
        }

        .site-main .our-team-section,
        .site-main .testimonials-section {
            max-width: 50%;
        }

        .our-team-section .team-flex,
        .testimonials-section .team-flex {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 3em;
        }

        .our-team-section h2 {
            font-size: 2em;
            font-weight: normal;
            max-width: 4.5em;
            padding-bottom: 1em;
            margin-bottom: 1em;
            margin-inline: auto;
        }

        .our-team-section h2::after,
        .testimonials-section h2::after {
            display: block;
            content: '';
            max-width: 3em;
            margin: 0 auto;
            padding-top: 0.5em;
            border-bottom: 0.2em solid #000;
            ;
        }

        .testimonials-section h2 {
            font-size: 2em;
            font-weight: normal;
            max-width: 5.7em;
            padding-bottom: 1em;
            margin-bottom: 1em;
            margin-inline: auto;
        }

        .card {
            max-width: 12em;
            text-align: center;
        }

        .card .media {
            overflow: hidden;
            border-radius: 1em;
            box-shadow: 0 0 0.5em 0 rgba(0, 0, 0, 0.5);
            margin-bottom: 1em;
            scale: 1;
            transition: scale 0.4s ease-in-out;
        }

        .card .media:hover {
            scale: 1.1;
        }

        .card p:first-of-type {
            margin-bottom: 1em;
        }

        .media img {
            display: block;
            width: 100%;
            height: auto;
        }

        .newsletter {
            background-color: #68347B;
            box-shadow: 0 0 1em 0 rgba(0, 0, 0, 0.5);
            height: 12em;
            align-self: stretch;
            display: flex;
            justify-content: center;
        }

        .newsletter h2 {
            color: #fff;
            font-weight: normal;
            margin: 1em 0 2em 0;
        }

        .newsletter h2::after {
            display: block;
            content: '';
            max-width: 3em;
            margin: 0 auto;
            padding-top: 0.5em;
            border-bottom: 0.2em solid #000;
        }

        .newsletter input[type="submit"] {
            padding: 0.5em 1em;
            color: #000;
            font-weight: bold;
            background-color: #fff;
            text-decoration: none;
            border: 0.1em solid #000;
            border-radius: 0.3em;
            transition: box-shadow 0.4s ease-in-out;
        }

        .newsletter input[type="submit"]:hover {
            box-shadow: 0 0 1em 0 rgba(0, 0, 0, 0.5);
        }

        .newsletter input {
            padding: 0.5em 1em;
            border: 0.1em solid #000;
            border-radius: 0.3em;
        }

        .sign-up-info {
            display: flex;
            align-items: center;
            gap: 0.5em;
        }

        /* footer css */

        .site-footer {
            margin-top: auto;
            padding-bottom: 6em;
            display: flex;
            justify-content: space-evenly;
        }

        .site-footer section {
            max-width: 30%;
        }

        .site-footer section p:first-child {
            color: #000;
            font-size: 1.5em;
            font-weight: bold;
        }

        .site-footer .site-map {
            max-width: 15%;
        }

        .site-footer .site-map nav ul {
            display: flex;
            flex-wrap: wrap;
            gap: 1em;
        }

        .site-footer ul li {
            display: block;
            list-style: none;
            margin-right: 1em;
        }

        .site-footer ul li a {
            color: #000;
            text-decoration: none;
            font-size: 1.2em;
        }

        .site-footer h3 {
            font-size: 1.5em;
            font-weight: normal;
            margin-bottom: 1em;
        }

        .site-footer i {
            margin-right: 0.5em;
        }

        @media (max-width: 800px) {

            html {
                font-size: 12px;
            }

            .site-footer {
                flex-direction: column;
                align-items: center;
                gap: 3em;
            }

            .site-footer section,
            .site-footer .site-map {
                max-width: 50%;
            }
        }

        @media (max-width: 1100px) {

            .site-header {
                flex-direction: column;
                gap: 1em;
            }

            .site-header p {
                text-align: center;
            }
        }
    </style>
</head>

<body>


    <header class="site-header">
        <p>My Website</p>
        <nav class="site-navigation">
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">About us</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Contacts</a></li>
            </ul>
        </nav>
    </header>

    <main class="site-main">
        <section class="main-header">
            <article>
                <h1>Need a website?</h1>
                <p>We do amazingly futuristic stuff using the same tools everyone has. We are so unique and awesome we didn't spend money on marketing gurus or copy writers!</p>
                <a href="#">Tell us about your project</a>
            </article>
            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                <path d="M57.7 193l9.4 16.4c8.3 14.5 21.9 25.2 38 29.8L163 255.7c17.2 4.9 29 20.6 29 38.5v39.9c0 11 6.2 21 16 25.9s16 14.9 16 25.9v39c0 15.6 14.9 26.9 29.9 22.6c16.1-4.6 28.6-17.5 32.7-33.8l2.8-11.2c4.2-16.9 15.2-31.4 30.3-40l8.1-4.6c15-8.5 24.2-24.5 24.2-41.7v-8.3c0-12.7-5.1-24.9-14.1-33.9l-3.9-3.9c-9-9-21.2-14.1-33.9-14.1H257c-11.1 0-22.1-2.9-31.8-8.4l-34.5-19.7c-4.3-2.5-7.6-6.5-9.2-11.2c-3.2-9.6 1.1-20 10.2-24.5l5.9-3c6.6-3.3 14.3-3.9 21.3-1.5l23.2 7.7c8.2 2.7 17.2-.4 21.9-7.5c4.7-7 4.2-16.3-1.2-22.8l-13.6-16.3c-10-12-9.9-29.5 .3-41.3l15.7-18.3c8.8-10.3 10.2-25 3.5-36.7l-2.4-4.2c-3.5-.2-6.9-.3-10.4-.3C163.1 48 84.4 108.9 57.7 193zM464 256c0-36.8-9.6-71.4-26.4-101.5L412 164.8c-15.7 6.3-23.8 23.8-18.5 39.8l16.9 50.7c3.5 10.4 12 18.3 22.6 20.9l29.1 7.3c1.2-9 1.8-18.2 1.8-27.5zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256z" />
            </svg>
        </section>

        <section class="our-team-section">
            <h2>Our Team</h2>
            <article class="team-flex">
                <div class="card">
                    <div class="media">
                        <img src="https://t3.ftcdn.net/jpg/02/43/12/34/360_F_243123463_zTooub557xEWABDLk0jJklDyLSGl2jrr.jpg" alt="">
                    </div>
                    <h3>Georgi Kostadinov</h3>
                    <p>CTO</p>
                    <p>Smart dude, geeky, knows his stuff! Nothing else.</p>
                </div>
                <div class="card">
                    <div class="media">
                        <img src="https://img.freepik.com/free-photo/portrait-handsome-man-with-dark-hairstyle-bristle-toothy-smile-dressed-white-sweatshirt-feels-very-glad-poses-indoor-pleased-european-guy-being-good-mood-smiles-positively-emotions-concept_273609-61405.jpg" alt="">
                    </div>
                    <h3>Lyuboslav Veliev</h3>
                    <p>Sales</p>
                    <p>Very agressive! Results driven.</p>
                </div>
                <div class="card">
                    <div class="media">
                        <img src="https://st3.depositphotos.com/1017228/18878/i/450/depositphotos_188781580-stock-photo-handsome-cheerful-young-man-standing.jpg" alt="">
                    </div>
                    <h3>Special Guy</h3>
                    <p>CEO</p>
                    <p>Drinks a lot. Loves meetings!</p>
                </div>
                <div class="card">
                    <div class="media">
                        <img src="https://t3.ftcdn.net/jpg/03/02/88/46/360_F_302884605_actpipOdPOQHDTnFtp4zg4RtlWzhOASp.jpg" alt="">
                    </div>
                    <h3>Some Guy</h3>
                    <p>SEO Optimisation</p>
                    <p>Drinks. Hates computers...</p>
                </div>
                <div class="card">
                    <div class="media">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8cGVyc29ufGVufDB8fDB8fHww&w=1000&q=80" alt="">
                    </div>
                    <h3>Generic Dude</h3>
                    <p>Front End</p>
                    <p>Cares too much about useless things</p>
                </div>
                <div class="card">
                    <div class="media">
                        <img src="https://images.pexels.com/photos/1222271/pexels-photo-1222271.jpeg?cs=srgb&dl=pexels-justin-shaifer-1222271.jpg&fm=jpg" alt="">
                    </div>
                    <h3>John Smith</h3>
                    <p>Lead Developer</p>
                    <p>We are not sure what he does!</p>
                </div>
            </article>

        </section>

        <section class="newsletter">
            <article>
                <h2>Sign-up to our newsletter</h2>
                <div class="sign-up-info">
                    <form action="#">
                        <input type="text">
                        <input type="submit">
                    </form>
                </div>
            </article>
        </section>

        <section class="testimonials-section">
            <h2>Testimonials</h2>
            <article class="team-flex">
                <div class="card">
                    <div class="media">
                        <img src="https://t3.ftcdn.net/jpg/02/43/12/34/360_F_243123463_zTooub557xEWABDLk0jJklDyLSGl2jrr.jpg" alt="">
                    </div>
                    <p><em>The most amazingly professional and absolutely great people I have had the pleasure to work with on the face of the earth.</em></p>
                    <h3>Georgi Kostadinov</h3>
                    <p>CTO</p>
                </div>
                <div class="card">
                    <div class="media">
                        <img src="https://img.freepik.com/free-photo/portrait-handsome-man-with-dark-hairstyle-bristle-toothy-smile-dressed-white-sweatshirt-feels-very-glad-poses-indoor-pleased-european-guy-being-good-mood-smiles-positively-emotions-concept_273609-61405.jpg" alt="">
                    </div>
                    <p><em>The most amazingly professional and absolutely great people I have had the pleasure to work with on the face of the earth.</em></p>
                    <h3>Lyuboslav Veliev</h3>
                    <p>Sales</p>
                </div>
                <div class="card">
                    <div class="media">
                        <img src="https://st3.depositphotos.com/1017228/18878/i/450/depositphotos_188781580-stock-photo-handsome-cheerful-young-man-standing.jpg" alt="">
                    </div>
                    <p><em>The most amazingly professional and absolutely great people I have had the pleasure to work with on the face of the earth.</em></p>
                    <h3>Special Guy</h3>
                    <p>CEO</p>
                </div>
                <div class="card">
                    <div class="media">
                        <img src="https://t3.ftcdn.net/jpg/03/02/88/46/360_F_302884605_actpipOdPOQHDTnFtp4zg4RtlWzhOASp.jpg" alt="">
                    </div>
                    <p><em>The most amazingly professional and absolutely great people I have had the pleasure to work with on the face of the earth.</em></p>
                    <h3>Some Guy</h3>
                    <p>SEO Optimisation</p>
                </div>
                <div class="card">
                    <div class="media">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8cGVyc29ufGVufDB8fDB8fHww&w=1000&q=80" alt="">
                    </div>
                    <p><em>The most amazingly professional and absolutely great people I have had the pleasure to work with on the face of the earth.</em></p>
                    <h3>Generic Dude</h3>
                    <p>Front End</p>
                </div>
                <div class="card">
                    <div class="media">
                        <img src="https://images.pexels.com/photos/1222271/pexels-photo-1222271.jpeg?cs=srgb&dl=pexels-justin-shaifer-1222271.jpg&fm=jpg" alt="">
                    </div>
                    <p><em>The most amazingly professional and absolutely great people I have had the pleasure to work with on the face of the earth.</em></p>
                    <h3>John Smith</h3>
                    <p>Lead Developer</p>
                </div>
            </article>

        </section>
    </main>

    <footer class="site-footer">
        <section>
            <p>My Website</p>
            <p>We are the best web gurus you will ever have the pleasure to encounter. We are amazing!</p>
        </section>

        <section class="site-map">
            <h3>Sitemap</h3>
            <nav>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About us</a></li>
                    <li><a href="#">Services</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Contacts</a></li>
                    <li><a href="#">Terms and Conditions</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </nav>
        </section>

        <section>
            <h3>Contacts</h3>
            <nav>
                <ul>
                    <li><i class="fa-solid fa-phone"></i><a href="#">0700 12 345</a></li>
                    <li><i class="fa-solid fa-envelope"></i><a href="#">office@purplespider.bg</a></li>
                    <li><i class="fa-solid fa-clock"></i><a href="#">Monday - Friday: 09:00 - 18:00</a></li>
                    <li><i class="fa-solid fa-map-location-dot"></i><a href="#">28 Some Place Str.</a></li>
                </ul>
            </nav>
        </section>
    </footer>
</body>

</html>