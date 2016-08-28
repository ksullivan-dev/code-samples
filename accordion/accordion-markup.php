<section class="layout-expanding-columns inview-trigger">
	<div class="width-container animateMe">
	    <h2 class="section-title stronglast accent"><?= $page["pagetitle"]; ?></h2>
        <?php $count = count( $loopingInputs ) <= 4 ? count( $loopingInputs ) : 4; ?>
	    <div class="column-container columns-<?= $count; ?>">
            <?php $counter = 0;
			foreach($loopingInputs as $loopingInput){
				$counter++;
				if( $counter > 4 ) break;
                $image = imagePath( $site_path ) . $loopingInput['image'];
				$active = $counter == 1 ? ' active' : ''; ?>
                <div class="column-slide slide-<?= $counter; ?><?= $active; ?>" style="background-image: url( <?php imageResize( 616, 395, 'crop' ); echo $image; ?> );">
    	            <h3 class="column-title"><span class="number-diamond">0<?= $counter; ?></span><?= $loopingInput['title']; ?></h3>
					<div class="column-content">
    	            	<p class="column-text"><?= $loopingInput['text']; ?></p>
    	            	<a href="<?= $loopingInput['link']['url']; ?>" class="column-link-text cta-link white"><?= $loopingInput['link']['title']; ?></a>
					</div>
                </div>
	        <?php } ?>
        </div>
    </div>
</section>
