<section class="layout-tabbed-list">
	<div class="width-container">
		<div class="values-list">
            <div class="values-list__links">
                <h2 class="values-list__heading"><?= $customInputs['list-heading']; ?></h2>
    			<ul class="values-list__list">
    				<?php foreach($loopingInputs as $key => $loopingInput){ ?>
    					<li class="values-list__item">
    						<a href="#values-list-<?= $key; ?>" class="values-list__link"><?= $loopingInput['item-heading']; ?></a>
    					</li>
    				<?php } ?>
    			</ul>
            </div>
			<div class="values-list__content">
				<?php foreach( $loopingInputs as $key => $loopingInput ) { ?>
					<div class="values-list__content-item" id="values-list-<?= $key; ?>">
						<h2 class="values-list__content-item__heading"><?= $loopingInput['item-heading']; ?></h2>
						<p class="values-list__content-item__detail"><?= $loopingInput['item-text']; ?></p>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</section>
