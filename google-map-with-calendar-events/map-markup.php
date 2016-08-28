<section class="layout-community-calendar">
    <div class="cc-filter-container">
        <div class="width-container">
            <h1 class="page-title"><?= $customInputs["title"]; ?></h1>
            <div class="all-filters-container">
                <div class="filter-container location-filter">
                    <h3 class="location-heading">Find events near you</h3>
                    <form>
                        <input type="text" class="location-search" placeholder="Search for your location..." />
                        <button class="location-search-button" value="Search">Search</button>
                        <span class="clear-search"></span>
                    </form>
                </div>
                <div class="filter-container time-filter">
                    <h3 class="time-heading">What time frame?</h3>
                    <div class="btn-group">
                        <a href="#" class="filter-week date-filter active">14 days</a>
                        <a href="#" class="filter-month date-filter">30 days</a>
                        <a href="#" class="filter-months6 date-filter">6 months</a>
                        <a href="#" class="filter-year date-filter">1 year</a>
                    </div>
                </div>
                <div class="filter-container type-filter">
                    <h3 class="type-heading">What types of events?</h3>
                    <form class="event-type-checkboxes">
                        <input class="event-type-checkbox" id="all-events" type="checkbox" name="event-type" value="All" checked /><label for="all-events">Show All Events</label>
                        <input class="event-type-checkbox" id="atc-only" type="checkbox" name="event-type" value="ATC" /><label for="atc-only">Show only Atlanta Track Club events</label>
                    </form>
                </div>
            </div>
            <div class="current-filters">
                <h3>Current Filters</h3>
                <div class="location-center current-filter" style="display: none;"></div>
                <div class="timeframe current-filter"></div>
                <div class="event-type current-filter"></div>
                <div class="change-btn-container">
                    <a href="#" class="change-filters">Change filters</a>
                </div>
            </div>
        </div>
    </div>
    <div class="calendar-map">
        <div class="map-container">
            <div id="map"></div>
            <?php include($_SERVER['DOCUMENT_ROOT'].'/'."sites/$site_path/exceptions/body/layouts/map-script.php"); ?>
        </div>
        <div class="event-container-outer">
            <div class="event-container-expand"></div>
            <div class="event-container"></div>
            <div class="event-sidebar-bottom">
                <p><?= $customInputs['sidebar-bottom']; ?></p>
                <?php if( $customInputs['button']['url'] ){ ?>
                    <a class="simple-btn" href="<?= $customInputs['button']['url']; ?>"><?= $customInputs['button']['title']; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
