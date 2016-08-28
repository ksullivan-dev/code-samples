<div class="width-container">
    <h2 class="section-title"><?= (isset($customInputs['heading']))?$customInputs['heading'] : "Recent Client Transactions" ?></h2>
    <div class="transactions-container">
        <div class="previous-card change-card">Previous</div>
        <div class="next-card change-card">Next</div>
        <?php $transactions = getCategoryPages( $pdo, 1854, $site_id );
            $transactionCounter = 1;
            foreach( $transactions as $transaction ){
                if( $transaction['layoutid'] != 1570 ) continue;
                if( $transactionCounter > 9 ) break;
                $title = $transaction['pagetitle'];
                $url = $transaction['url'];
                $txdata = json_decode( $transaction['pagedata'], true );
                $companyLogo1 = $txdata['company-logo-1'];
                $companyLogo2 = $txdata['company-logo-2'];
                $company1 = $txdata['company-name-1'];
                $company2 = $txdata['company-name-2'];
                $date = date( 'F Y', strtotime( $txdata['transaction-date'] ) );
                $testimonialName = $txdata['testimonial-name'];
                $testimonialTitle = $txdata['testimonial-title'];
                $testimonialCompany = $txdata['testimonial-company'];
                $testimonialText = $txdata['testimonial-text'];
                $hidden = $transactionCounter > 3 ? ' flip-card--hidden' : '';
                ?>
                <div class="flip-card recent-transactions-card<?= $hidden; ?>">
                    <div class="transaction-card flip-card__front">
                        <a href="<?= $url; ?>" class="transaction-card__heading-link"><span class="transaction-card__heading-text"><?= $title; ?></span></a>
                        <?php if( $companyLogo1 || $companyLogo2 ) { ?>
                            <?php $multi = $companyLogo1 && $companyLogo2 ? ' transaction-card__logo--multi' : ''; ?>
                            <div class="transaction-card__logo-container">
                                <?php if( $companyLogo1 ){ ?>
                                    <div class="transaction-card__logo<?= $multi; ?>">
                                        <?php srcSet( imagePath( $site_path ) . $companyLogo1, $company1, 108, 86, 'fit' ); ?>
                                    </div>
                                <?php }
                                if( $companyLogo2 ){ ?>
                                    <div class="transaction-card__logo<?= $multi; ?>">
                                        <?php srcSet( imagePath( $site_path ) . $companyLogo2, $company2, 108, 86, 'fit' ); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if( $txdata['transaction-date'] ){ ?>
                            <div class="transaction-card__date"><?= $date; ?></div>
                        <?php } ?>
                        <div class="transaction-card__link-container">
                            <?php if( $testimonialText ){ ?>
                                <a href="#" class="transaction-card__link flip-card__flipper"><i class="fa fa-angle-up"></i> View Our Customer's Testimonial <i class="fa fa-angle-up"></i></a>
                            <?php } else { ?>
                                <a href="<?= $url; ?>" class="transaction-card__link">View the Transaction <i class="fa fa-angle-right"></i></a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if( $testimonialText ) { ?>
                        <div class="transaction-card transaction-card__testimonial flip-card__back">
                            <div class="flip-card__close"></div>
                            <h4 class="testimonial-card__name"><?= $testimonialName; ?></h4>
                            <h5 class="testimonial-card__info"><?= $testimonialTitle; ?> | <?= $testimonialCompany; ?></h5>
                            <p class="testimonial-card__text"><?= $testimonialText; ?></p>
                            <div class="testimonial-card__link-container transaction-card__link-container">
                                <a href="<?= $url; ?>" class="testimonial-card__link transaction-card__link">View the Transaction <i class="fa fa-angle-right"></i></a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php $transactionCounter++;
            } ?>
    </div>
</div>
