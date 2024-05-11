<section>
    <h2>About Us</h2>
    <div class="about-content">
        <img src="./images/about/store.webp"
             alt="Family-owned Sporting Goods Store">
        <div>
            <p class="about-us">
                We are a small family-owned business that has been selling
                sporting goods for over 30 years. We
                started our journey in 1994 with a mission to provide
                high-quality sports equipment to our local community. Since
                then, we've expanded our offerings to cater to sports
                enthusiasts of all ages and skill levels.
            </p>
            <p class="about-us">
                We're committed to continuously updating our inventory with the
                latest and most effective sports gear available. From
                traditional sports like soccer and basketball to emerging
                activities such as paddle sports and adventure racing, we ensure
                a diverse and comprehensive selection.
            </p>
            <p class="about-us">
                We invite you to visit us online or in-store to see what's new
                and exciting. Join our community on social media to stay updated
                on our latest products, promotions, and events. At Sports Warehouse, your passion for sports is celebrated.
            </p>
            <p class="call-to-action">
                Come see why we've been a favorite among local athletes for
                decades!
            </p>
        </div>
    </div>
</section>

<style>
    .about-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        margin: 0 20px;
    }

    .about-content img {
        max-height: 300px;
        object-fit: cover;
        margin-bottom: 20px;
        margin-right: 0;
    }

    @media (min-width: 850px) {
        .about-content {
            flex-direction: row;
        }

        .about-content img {
            margin-bottom: 0;
            margin-right: 20px;
        }
    }

    .about-us {
        flex: 1;
        font-size: 16px;
        line-height: 1.5;
        margin-bottom: 20px;
    }
</style>