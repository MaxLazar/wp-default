<?php

class ShortPixelView {
    
    private $ctrl;
    
    public function __construct($controller) {
        $this->ctrl = $controller;
    }
    
        //handling older
    public function ShortPixelView($controller) {
        $this->__construct($controller);
    }

    public function displayQuotaExceededAlert($quotaData, $averageCompression = false, $recheck = false) 
    { ?>    
        <br/>
        <div class="wrap sp-quota-exceeded-alert"  id="short-pixel-notice-exceed">
            <?php if($averageCompression) { ?>
            <div style="float:right; margin-top: 10px">
                <div class="bulk-progress-indicator" style="height: 110px">
                    <div style="margin-bottom:5px"><?php _e('Average image<br>reduction so far:','shortpixel-image-optimiser');?></div>
                    <div id="sp-avg-optimization"><input type="text" id="sp-avg-optimization-dial" value="<?php echo("" . round($averageCompression))?>" class="dial"></div>
                    <script>
                        jQuery(function() {
                            ShortPixel.percentDial("#sp-avg-optimization-dial", 60);
                        });
                    </script>
                </div>
            </div>
            <?php } ?>
            <h3><?php /* translators: header of the alert box */ _e('Quota Exceeded','shortpixel-image-optimiser');?></h3>
            <p><?php /* translators: body of the alert box */ 
                if($recheck) {
                     echo('<span style="color: red">' . __('You have no available image credits. If you just bought a package, please note that sometimes it takes a few minutes for the payment confirmation to be sent to us by the payment processor.','shortpixel-image-optimiser') . '</span><br>');
                }
                printf(__('The plugin has optimized <strong>%s images</strong> and stopped because it reached the available quota limit.','shortpixel-image-optimiser'), 
                      number_format(max(0, $quotaData['APICallsMadeNumeric'] + $quotaData['APICallsMadeOneTimeNumeric'])));?> 
            <?php if($quotaData['totalProcessedFiles'] < $quotaData['totalFiles']) { ?>
                <?php 
                    printf(__('<strong>%s images and %s thumbnails</strong> are not yet optimized by ShortPixel.','shortpixel-image-optimiser'),
                        number_format(max(0, $quotaData['mainFiles'] - $quotaData['mainProcessedFiles'])), 
                        number_format(max(0, ($quotaData['totalFiles'] - $quotaData['mainFiles']) - ($quotaData['totalProcessedFiles'] - $quotaData['mainProcessedFiles'])))); ?>
                <?php } ?></p>
            <div> <!-- style='float:right;margin-top:20px;'> -->
                <a class='button button-primary' href='https://shortpixel.com/login/<?php echo($this->ctrl->getApiKey());?>' target='_blank'><?php _e('Upgrade','shortpixel-image-optimiser');?></a>
                <input type='button' name='checkQuota' class='button' value='<?php _e('Confirm New Quota','shortpixel-image-optimiser');?>' 
                       onclick="ShortPixel.recheckQuota()">
            </div>
            <p><?php _e('Get more image credits by referring ShortPixel to your friends!','shortpixel-image-optimiser');?> 
                <a href="https://shortpixel.com/login/<?php echo(defined("SHORTPIXEL_HIDE_API_KEY") ? '' : $this->ctrl->getApiKey());?>/tell-a-friend" target="_blank">
                    <?php _e('Check your account','shortpixel-image-optimiser');?>
                </a> <?php _e('for your unique referral link. For each user that joins, you will receive +100 additional image credits/month.','shortpixel-image-optimiser');?>
            </p>
            
        </div> <?php 
    }
    
    public static function displayApiKeyAlert() 
    { ?>
        <p><?php _e('In order to start the optimization process, you need to validate your API Key in the '
                . '<a href="options-general.php?page=wp-shortpixel">ShortPixel Settings</a> page in your WordPress Admin.','shortpixel-image-optimiser');?>
        </p>
        <p><?php _e('If you don’t have an API Key, you can get one delivered to your inbox, for free.','shortpixel-image-optimiser');?></p>
        <p><?php _e('Please <a href="https://shortpixel.com/wp-apikey" target="_blank">sign up to get your API key.</a>','shortpixel-image-optimiser');?>
        </p>
    <?php
    }
    
    public static function displayActivationNotice($when = 'activate', $extra = '')  { 
        $extraStyle = $when == 'compat' ? "style='border-left: 4px solid#ff0000;'" : '';
        ?>
        <div class='notice notice-warning' id='short-pixel-notice-<?php echo($when);?>' <?php echo($extraStyle);?>>
            <?php if($when != 'activate') { ?>
            <div style="float:right;"><a href="javascript:dismissShortPixelNotice('<?php echo($when);?>')" class="button" style="margin-top:10px;"><?php _e('Dismiss','shortpixel-image-optimiser');?></a></div>
            <?php } ?>
            <h3><?php 
            if($when == 'compat') {_e('Warning','shortpixel-image-optimiser'); echo(' - ');}
            _e('ShortPixel Image Optimizer','shortpixel-image-optimiser');?></h3> <?php
            switch($when) {
                case '2h' : 
                    _e("Action needed. Please <a href='https://shortpixel.com/wp-apikey' target='_blank'>get your API key</a> to activate your ShortPixel plugin.",'shortpixel-image-optimiser') . "<BR><BR>";
                    break;
                case '3d':
                    _e("Your image gallery is not optimized. It takes 2 minutes to <a href='https://shortpixel.com/wp-apikey' target='_blank'>get your API key</a> and activate your ShortPixel plugin.",'shortpixel-image-optimiser') . "<BR><BR>";
                    break;
                case 'activate':
                    self::displayApiKeyAlert();
                    break;
                case 'compat' :
                    _e("Using ShortPixel while other image optimization plugins are active can lead to unpredictable results. We recommend to deactivate the following plugin(s): ",'shortpixel-image-optimiser');
                    echo('<ul>');
                    foreach($extra as $plugin) {
                        echo('<li class="sp-conflict-plugins-list"><strong>' . $plugin['name'] . '</strong>');
                        echo('<a href="' . wp_nonce_url( admin_url( 'admin-post.php?action=shortpixel_deactivate_plugin&plugin=' . urlencode( $plugin['path'] ) ), 'sp_deactivate_plugin_nonce' ) . '" class="button">'
                                . __( 'Deactivate', 'shortpixel_image_optimiser' ) . '</a>');
                    }
                    echo("</ul>");
                    break;
                case 'upgmonth' :
                case 'upgbulk' : ?>
                    <p> <?php
                    if($when == 'upgmonth') {
                        printf(__("You are adding an average of <strong>%d images and thumbnails every month</strong> to your Media Library and you have <strong>a plan of %d images/month</strong>." 
                              . " You might need to upgrade you plan in order to have all your images optimized.", 'shortpixel_image_optimiser'), $extra['monthAvg'], $extra['monthlyQuota']);
                    } else {
                    printf(__("You currently have <strong>%d images and thumbnails to optimize</strong> in your Media Library but you only you have <strong>%d images</strong> available in your current plan." 
                              . " You might need to upgrade you plan in order to have all your images optimized.", 'shortpixel_image_optimiser'), $extra['filesTodo'], $extra['quotaAvailable']);
                    }?>
                    <br><br>
                    <button class="button button-primary" id="shortpixel-upgrade-advice" onclick="ShortPixel.proposeUpgrade()"><strong>
                         <?php _e('Show me the best available options', 'shortpixel_image_optimiser'); ?></strong></button>
                    </p>
                    <div id="shortPixelProposeUpgradeShade" class="sp-modal-shade" style="display:none;">
                        <div id="shortPixelProposeUpgrade" class="shortpixel-modal shortpixel-hide">
                            <div class="sp-modal-title">
                                <button type="button" class="sp-close-upgrade-button" onclick="ShortPixel.closeProposeUpgrade()">&times;</button>
                                <?php _e('Upgrade your ShortPixel account', 'shortpixel-image-optimiser');?>
                            </div>
                            <div class="sp-modal-body sptw-modal-spinner" style="height:400px;padding:0;">
                            </div>
                        </div>
                    </div>
                    <?php break;
                case 'generic' :
                    echo("<p>$extra</p>");
                    break;
            }
            ?>
        </div>
    <?php
    }
    
    public function displayBulkProcessingForm($quotaData,  $thumbsProcessedCount, $under5PercentCount, $bulkRan, 
                                              $averageCompression, $filesOptimized, $savedSpace, $percent, $customCount) {
        ?>
        <div class="wrap short-pixel-bulk-page">
            <h1>Bulk Image Optimization by ShortPixel</h1>
        <?php
        if ( !$bulkRan ) {
            ?>
            <div class="sp-notice sp-notice-info sp-floating-block sp-full-width">
                <form class='start' action='' method='POST' id='startBulk'>
                    <input type='hidden' id='mainToProcess' value='<?php echo($quotaData['mainFiles'] - $quotaData['mainProcessedFiles']);?>'/>
                    <input type='hidden' id='totalToProcess' value='<?php echo($quotaData['totalFiles'] - $quotaData['totalProcessedFiles']);?>'/>
                    <div class="bulk-stats-container">
                        <h3 style='margin-top:0;'><?php _e('Your media library','shortpixel-image-optimiser');?></h3>
                        <div class="bulk-label"><?php _e('Original images','shortpixel-image-optimiser');?></div>
                        <div class="bulk-val"><?php echo(number_format($quotaData['mainMlFiles']));?></div><br>
                        <div class="bulk-label"><?php _e('Smaller thumbnails','shortpixel-image-optimiser');?></div>
                        <div class="bulk-val"><?php echo(number_format($quotaData['totalMlFiles'] - $quotaData['mainMlFiles']));?></div>
                        <div style='width:165px; display:inline-block; padding-left: 5px'>
                            <input type='checkbox' id='thumbnails' name='thumbnails' onclick='ShortPixel.checkThumbsUpdTotal(this)' <?php echo($this->ctrl->processThumbnails() ? "checked":"");?>> 
                            <?php _e('Include thumbnails','shortpixel-image-optimiser');?>
                        </div><br>
                        <?php if($quotaData["totalProcessedMlFiles"] > 0) { ?>
                        <div class="bulk-label bulk-total"><?php _e('Total images','shortpixel-image-optimiser');?></div>
                        <div class="bulk-val bulk-total"><?php echo(number_format($quotaData['totalMlFiles']));?></div>
                        <br><div class="bulk-label"><?php _e('Already optimized originals','shortpixel-image-optimiser');?></div>
                        <div class="bulk-val"><?php echo(number_format($quotaData['mainProcessedMlFiles']));?></div><br>
                        <div class="bulk-label"><?php _e('Already optimized thumbnails','shortpixel-image-optimiser');?></div>
                        <div class="bulk-val"><?php echo(number_format($quotaData['totalProcessedMlFiles'] - $quotaData['mainProcessedMlFiles']));?></div><br>
                        <?php } ?>
                        <div class="bulk-label bulk-total"><?php _e('Total to be optimized','shortpixel-image-optimiser');?></div>
                        <div class="bulk-val bulk-total" id='displayTotal'><?php echo(number_format(max( 0, $quotaData['totalMlFiles'] - $quotaData['totalProcessedMlFiles'])));?></div>

                        <?php if($customCount > 0) { ?>
                        <h3 style='margin-bottom:10px;'><?php _e('Your custom folders','shortpixel-image-optimiser');?></h3>
                        <div class="bulk-label bulk-total"><?php _e('Total to be optimized','shortpixel-image-optimiser');?></div>
                        <div class="bulk-val bulk-total" id='displayTotal'><?php echo(number_format($customCount));?></div>                        
                        <?php  } ?>
                    </div>
                    <?php if(max(0, $quotaData['totalMlFiles'] - $quotaData['totalProcessedMlFiles']) + $customCount > 0) { ?>
                    <div class="bulk-play">
                        <input type='hidden' name='bulkProcess' id='bulkProcess' value='Start Optimizing'/>
                        <a href='javascript:void(0);' onclick="document.getElementById('startBulk').submit();" class='button'>
                            <div style="width: 320px">
                                <div class="bulk-btn-img" class="bulk-btn-img">
                                    <img src='<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/robo-slider.png' ));?>'/>
                                </div>
                                <div  class="bulk-btn-txt">
                                    <?php printf(__('<span class="label">Start Optimizing</span><br> <span class="total">%s</span> images','shortpixel-image-optimiser'),
                                            $this->ctrl->processThumbnails() ?
                                                number_format(max(0, $quotaData['totalMlFiles'] - $quotaData['totalProcessedMlFiles']) + $customCount) :
                                                number_format(max(0, $quotaData['mainMlFiles'] - $quotaData['mainProcessedMlFiles']) + $customCount));?>
                                </div>
                                <div class="bulk-btn-img" class="bulk-btn-img">
                                    <img src='<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/arrow.png' ));?>'/>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php if($quotaData['mainProcessedMlFiles'] > 0) {?>
                    <div style="position: absolute;bottom: 10px;right: 10px;">
                        <input type='submit' name='bulkRestore' id='bulkRestore' class='button' value='<?php _e('Bulk Restore Media Library','shortpixel-image-optimiser');?>' onclick="ShortPixel.confirmBulkAction('Restore', event)" style="margin-bottom:10px;"><br>
                        <input type='submit' name='bulkCleanup' id='bulkRestore' class='button' value='<?php _e('Bulk Delete Metadata','shortpixel-image-optimiser');?>' onclick="ShortPixel.confirmBulkAction('Cleanup', event)" style="width:100%">
                    </div>
                        
                    <?php }
                    }  else {?>
                    <div class="bulk-play bulk-nothing-optimize">
                        <?php _e('Nothing to optimize! The images that you add to Media Gallery will be automatically optimized after upload.','shortpixel-image-optimiser');?>
                    </div>
                    <?php } ?>
                </form>
            </div>
            <?php if($quotaData['totalFiles'] - $quotaData['totalProcessedFiles'] > 0) { ?>
                <div class='shortpixel-clearfix'></div>
                <div class="bulk-wide">
                    <h3 style='font-size: 1.1em; font-weight: bold;'>
                        <?php _e('After you start the bulk process, in order for the optimization to run, you must keep this page open and your computer running. If you close the page for whatever reason, just turn back to it and the bulk process will resume.','shortpixel-image-optimiser');?>
                    </h3>
                </div>
            <?php } ?>
            <div class='shortpixel-clearfix'></div>
            <div class="bulk-text-container">
                <h3><?php _e('What are Thumbnails?','shortpixel-image-optimiser');?></h3>
                <p><?php _e('Thumbnails are smaller images usually generated by your WP theme. Most themes generate between 3 and 6 thumbnails for each Media Library image.','shortpixel-image-optimiser');?></p>
                <p><?php _e("The thumbnails also generate traffic on your website pages and they influence your website's speed.",'shortpixel-image-optimiser');?></p>
                <p><?php _e("It's highly recommended that you include thumbnails in the optimization as well.",'shortpixel-image-optimiser');?></p>
            </div>
            <div class="bulk-text-container" style="padding-right:0">
                <h3><?php _e('How does it work?','shortpixel-image-optimiser');?></h3>
                <p><?php _e('The plugin processes images starting with the newest ones you uploaded in your Media Library.','shortpixel-image-optimiser');?></p>
                <p><?php _e('You will be able to pause the process anytime.','shortpixel-image-optimiser');?></p>
                <p><?php echo($this->ctrl->backupImages() ? __("<p>Your original images will be stored in a separate back-up folder.</p>",'shortpixel-image-optimiser') : "");?></p>
                <p><?php _e('You can watch the images being processed live, right here, after you start optimizing.','shortpixel-image-optimiser');?></p>
            </div>
            <?php
        } elseif($percent) // bulk is paused
        { ?>
            <?php echo($this->displayBulkProgressBar(false, $percent, "", $quotaData['APICallsRemaining'], $this->ctrl->getAverageCompression(), 1, $customCount));?>
            <p><?php _e('Please see below the optimization status so far:','shortpixel-image-optimiser');?></p>
            <?php $this->displayBulkStats($quotaData['totalProcessedFiles'], $quotaData['mainProcessedFiles'], $under5PercentCount, $averageCompression, $savedSpace);?>
            <?php if($quotaData['totalProcessedFiles'] < $quotaData['totalFiles']) { ?>
                <p><?php printf(__('%d images and %d thumbnails are not yet optimized by ShortPixel.','shortpixel-image-optimiser'),
                                number_format($quotaData['mainFiles'] - $quotaData['mainProcessedFiles']),
                                number_format(($quotaData['totalFiles'] - $quotaData['mainFiles']) - ($quotaData['totalProcessedFiles'] - $quotaData['mainProcessedFiles'])));?> 
                </p>
            <?php } ?>
            <p><?php _e('You can continue optimizing your Media Gallery from where you left, by clicking the Resume processing button. Already optimized images will not be reprocessed.','shortpixel-image-optimiser');?></p>
        <?php
        } else { ?>
            <div class="sp-container">
                <div class='sp-notice sp-notice-success sp-floating-block sp-single-width' style="height: 80px;overflow:hidden;">
                    <div style='float:left;margin:5px 20px 5px 0'>
                        <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/slider.png' ));?>"
                             srcset='<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/slider.png' ));?> 1x, <?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/slider@2x.png' ));?> 2x'>
                    </div>
                    <div class="sp-bulk-summary">
                        <input type="text" value="<?php echo("" . round($averageCompression))?>" id="sp-total-optimization-dial" class="dial">
                    </div>
                    <p style="margin-top:4px;">
                        <span style="font-size:1.2em;font-weight:bold"><?php _e('Congratulations!','shortpixel-image-optimiser');?></span><br>
                        <?php _e('Your media library has been successfully optimized!','shortpixel-image-optimiser');?>
                        <span class="sp-bulk-summary"><a href='javascript:void(0);'><?php _e('Summary','shortpixel-image-optimiser');?></a></span>
                    </p>
                </div>
                <div class='sp-notice sp-notice-success sp-floating-block sp-single-width' style="height: 80px;overflow:hidden;padding-right: 0;">
                    <div style="float:left; margin-top:-5px">
                        <p style='margin-bottom: -2px; font-weight: bold;'>
                            <?php _e('Share your optimization results:','shortpixel-image-optimiser');?>
                        </p>
                        <div style='display:inline-block; margin: 16px 16px 6px 0;float:left'>
                            <div id="fb-root"></div>
                            <script>
                                (function(d, s, id) {
                                    var js, fjs = d.getElementsByTagName(s)[0];
                                    if (d.getElementById(id)) return;
                                    js = d.createElement(s); js.id = id;
                                    js.src = "//connect.facebook.net/<?php echo(get_locale());?>/sdk.js#xfbml=1&version=v2.6";
                                    fjs.parentNode.insertBefore(js, fjs);
                                }(document, 'script', 'facebook-jssdk'));
                            </script>
                            <div style="float:left;width:240px;">
                                <div class="fb-like" data-href="https://www.facebook.com/ShortPixel" data-width="260" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>
                            </div>
                            <div style="float:left;margin:-7px 0 0 10px">
                                <a href="https://twitter.com/share" class="twitter-share-button" data-url="https://shortpixel.com" 
                                   data-text="<?php 
                                        if(0+$averageCompression>20) {
                                            _e("I just #optimized my site's images by ",'shortpixel-image-optimiser');
                                        } else {
                                            _e("I just #optimized my site's images ",'shortpixel-image-optimiser');
                                        }
                                        echo(round($averageCompression) ."%");
                                        echo(__("with @ShortPixel, a #WordPress image optimization plugin",'shortpixel-image-optimiser') . " #pagespeed #seo");?>" 
                                   data-size='large'><?php _e('Tweet','shortpixel-image-optimiser');?></a>
                            </div>
                            <script>
                                jQuery(function() {
                                    jQuery("#sp-total-optimization-dial").val("<?php echo("" . round($averageCompression))?>");
                                    ShortPixel.percentDial("#sp-total-optimization-dial", 60);
                                    
                                    jQuery(".sp-bulk-summary").spTooltip({
                                        tooltipSource: "inline",
                                        tooltipSourceID: "#sp-bulk-stats"
                                    });
                                });
                                !function(d,s,id){//Just optimized my site with ShortPixel image optimization plugin
                                    var js,
                                        fjs=d.getElementsByTagName(s)[0],
                                        p=/^http:/.test(d.location)?'http':'https';
                                    if(!d.getElementById(id)){js=d.createElement(s);
                                    js.id=id;js.src=p+'://platform.twitter.com/widgets.js';
                                    fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
                            </script>
                        </div>
                    </div>
                    <?php if(0+$averageCompression>30) {?> 
                    <div class='shortpixel-rate-us' style='float:left;padding-top:0'>
                        <a href="https://wordpress.org/support/view/plugin-reviews/shortpixel-image-optimiser?rate=5#postform" target="_blank">
                            <span>
                                <?php _e('Please rate us!','shortpixel-image-optimiser');?>&nbsp;
                            </span><br><img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/stars.png' ));?>">
                        </a>
                    </div>
                    <?php } ?>
                </div>
                <div id="sp-bulk-stats" style="display:none">
                    <?php $this->displayBulkStats($quotaData['totalProcessedFiles'], $quotaData['mainProcessedFiles'], $under5PercentCount, $averageCompression, $savedSpace);?>
                </div>            
            </div>
            <p><?php printf(__('Go to the ShortPixel <a href="%soptions-general.php?page=wp-shortpixel#stats">Stats</a> '
                             . 'and see all your websites\' optimized stats. Download your detailed <a href="https://api.shortpixel.com/v2/report.php?key=%s">Optimization Report</a> '
                             . 'to check your image optimization statistics for the last 40 days.','shortpixel-image-optimiser'), 
                             get_admin_url(), (defined("SHORTPIXEL_HIDE_API_KEY") ? '' : $this->ctrl->getApiKey()) );?></p>
            <?php 
            $failed = $this->ctrl->getPrioQ()->getFailed();
            if(count($failed)) { ?>
                <div class="bulk-progress" style="margin-bottom: 15px">
                    <p>
                        <?php _e('The following images could not be processed because of their limited write rights. This usually happens if you have changed your hosting provider. Please restart the optimization process after you granted write rights to all the files below.','shortpixel-image-optimiser');?>
                    </p>
                    <?php $this->displayFailed($failed); ?>
                </div>
            <?php } ?>
            <div class="bulk-progress sp-notice sp-notice-info sp-floating-block sp-double-width">
                <?php
                $todo = $reopt = false;
                if($quotaData['totalProcessedFiles'] < $quotaData['totalFiles']) { 
                    $todo = true;
                    $mainNotProcessed = max(0, $quotaData['mainFiles'] - $quotaData['mainProcessedFiles']);
                    $thumbsNotProcessed = max(0, ($quotaData['totalFiles'] - $quotaData['mainFiles']) - ($quotaData['totalProcessedFiles'] - $quotaData['mainProcessedFiles']));
                    ?>
                    <p>
                        <?php 
                        if($mainNotProcessed && $thumbsNotProcessed) {
                            printf(__("%s images and %s thumbnails are not yet optimized by ShortPixel.",'shortpixel-image-optimiser'), 
                                    number_format($mainNotProcessed), number_format($thumbsNotProcessed)); 
                        } elseif($mainNotProcessed) {
                            printf(__("%s images are not yet optimized by ShortPixel.",'shortpixel-image-optimiser'), number_format($mainNotProcessed)); 
                        } elseif($thumbsNotProcessed) {
                            printf(__("%s thumbnails are not yet optimized by ShortPixel.",'shortpixel-image-optimiser'), number_format($thumbsNotProcessed)); 
                        }
                        _e('','shortpixel-image-optimiser');
                        if (count($quotaData['filesWithErrors'])) { 
                            _e('Some have errors:','shortpixel-image-optimiser'); echo(' ');
                            foreach($quotaData['filesWithErrors'] as $id => $data) {
                                if(ShortPixelMetaFacade::isCustomQueuedId($id)) {
                                    echo('<a href="'. ShortPixelMetaFacade::getHomeUrl() . ShortPixelMetaFacade::filenameToRootRelative($data['Path']).'" title="'.$data['Message'].'" target="_blank">'.$data['Name'].'</a>,&nbsp;');
                                } else {
                                    echo('<a href="post.php?post='.$id.'&action=edit" title="'.$data['Message'].'">'.$data['Name'].'</a>,&nbsp;');
                                }
                            } 
                        } ?>
                    </p>
                <?php }
                $settings = $this->ctrl->getSettings();
                $optType = ShortPixelAPI::getCompressionTypeName($settings->compressionType);
                $otherTypes = ShortPixelAPI::getCompressionTypeName($this->ctrl->getOtherCompressionTypes($settings->compressionType));
                $extraW = $extraO = '';
                if(   !$this->ctrl->backupFolderIsEmpty()
                   && (   ($quotaData['totalProcLossyFiles'] > 0 && $settings->compressionType != 1)
                       || ($quotaData['totalProcGlossyFiles'] > 0 && $settings->compressionType != 2) 
                       || ($quotaData['totalProcLosslessFiles'] > 0 && $settings->compressionType != 0)))
                {     
                    $todo = $reopt = true;
                    $statType = ucfirst($otherTypes[0]);
                    $thumbsCount = $quotaData['totalProc'.$statType.'Files'] - $quotaData['mainProc'.$statType.'Files'];
                    
                    $statType2 = ucfirst($otherTypes[1]);
                    $thumbsCount2 = $quotaData['totalProc'.$statType2.'Files'] - $quotaData['mainProc'.$statType2.'Files'];
                    if($quotaData['totalProc'.$statType2.'Files'] > 0 ) {
                        if($quotaData['totalProc'.$statType.'Files'] > 0) {
                            $extraW = sprintf(__('%s images and %s thumbnails were optimized <strong>%s</strong>. ','shortpixel-image-optimiser'), 
                                 number_format($quotaData['mainProc'.$statType2.'Files']),
                                 number_format($thumbsCount2), $otherTypes[1]);
                            $extraO = sprintf(__('%s images were optimized <strong>%s</strong>. ','shortpixel-image-optimiser'), 
                                 number_format($quotaData['mainProc'.$statType2.'Files']), $otherTypes[1]);
                        } else {
                            $extraW = $extraO = ''; $otherTypes[0] = $otherTypes[1]; $statType = $statType2;
                        }
                    }
                    ?>
                    <p id="with-thumbs" <?php echo(!$settings->processThumbnails ? 'style="display:none;"' : "");?>>
                        <?php echo($extraW);
                            printf(__('%s images and %s thumbnails were optimized <strong>%s</strong>. You can re-optimize <strong>%s</strong> the ones that have backup.','shortpixel-image-optimiser'), 
                                     number_format($quotaData['mainProc'.$statType.'Files']),
                                     number_format($thumbsCount), $otherTypes[0], $optType);?>
                    </p>
                    <p id="without-thumbs" <?php echo($settings->processThumbnails ? 'style="display:none;"' : "");?>>
                        <?php  echo($extraO); 
                            printf(__('%s images were optimized <strong>%s</strong>. You can re-optimize <strong>%s</strong> the ones that have backup. ','shortpixel-image-optimiser'), 
                                     number_format($quotaData['mainProc'.$statType.'Files']),
                                     $otherTypes[0], $optType);?>
                        <?php echo($thumbsCount + $thumbsCount2 ? number_format($thumbsCount + $thumbsCount2) . __(' thumbnails will be restored to originals.','shortpixel-image-optimiser') : '');?>
                    </p>
                    <?php
                } ?>
                <p><?php if($todo) {
                        _e('Restart the optimization process for these images by clicking the button below.','shortpixel-image-optimiser');
                    } else {
                        _e('Restart the optimization process for new images added to your library by clicking the button below.','shortpixel-image-optimiser');
                    }
                    echo(' ');
                    printf(__('Already  <strong>%s</strong> optimized images will not be reprocessed.','shortpixel-image-optimiser'), $todo ? ($optType) : '');
                    if($reopt) { ?>
                    <br><?php _e('Please note that reoptimizing images as <strong>lossy/lossless</strong> may use additional credits.','shortpixel-image-optimiser')?> 
                    <a href="http://blog.shortpixel.com/the-all-new-re-optimization-functions-in-shortpixel/" target="_blank"><?php _e('More info','shortpixel-image-optimiser');?></a>
                    <?php } ?>
                </p>
                <form action='' method='POST' >
                    <input type='checkbox' id='bulk-thumbnails' name='thumbnails' <?php echo($this->ctrl->processThumbnails() ? "checked":"");?> 
                           onchange="ShortPixel.onBulkThumbsCheck(this)"> <?php _e('Include thumbnails','shortpixel-image-optimiser');?><br><br>
                    <input type='submit' name='bulkProcess' id='bulkProcess' class='button button-primary' value='<?php _e('Restart Optimizing','shortpixel-image-optimiser');?>'>
                    <input type='submit' name='bulkRestore' id='bulkRestore' class='button' value='<?php _e('Bulk Restore Media Library','shortpixel-image-optimiser');?>' onclick="ShortPixel.confirmBulkAction('Restore',event)" style="float: right;">
                    <input type='submit' name='bulkCleanup' id='bulkRestore' class='button' value='<?php _e('Bulk Delete Metadata','shortpixel-image-optimiser');?>' onclick="ShortPixel.confirmBulkAction('Cleanup',event)" style="float: right;margin-right:10px;">
                </form>
            </div>
        <?php } ?>
        </div>
        <?php
    }

    public function displayBulkProcessingRunning($percent, $message, $remainingQuota, $averageCompression, $type) {
        $settings = $this->ctrl->getSettings();
        $dismissed = $settings->dismissedNotices ? $settings->dismissedNotices : array();
        ?>
        <div class="wrap short-pixel-bulk-page">
            <h1><?php _e('Bulk Image Optimization by ShortPixel','shortpixel-image-optimiser');?></h1>
            <?php $this->displayBulkProgressBar(true, $percent, $message, $remainingQuota, $averageCompression, $type);?>

            <!-- Partners: SQUIRLY -->
            <?php if(!isset($dismissed['squirrly'])) { ?>
           <div id="short-pixel-notice-squirrly" class="sp-notice sp-notice-info bulk-progress bulk-progress-partners sp-floating-block sp-full-width">
                <div style="float:right"><a href="javascript:dismissShortPixelNotice('squirrly')"><?php _e('Dismiss','shortpixel-image-optimiser');?></a></div>
                <a href="https://my.squirrly.co/go120073/squirrly.co/short-pixel-seo" target="_blank">
                    <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/squirrly.png' ));?>" height="50">
                    <div><?php _e('While you wait for your images to optimize, check out Squirrly, a great plugin for further boosting your SEO.','shortpixel-image-optimiser');?></div>
                </a>
            </div>
            <?php } ?>

            <div class="sp-floating-block sp-notice bulk-notices-parent">
                <div class="bulk-notice-container">
                    <div class="bulk-notice-msg bulk-lengthy">
                        <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/loading-dark-big.gif' ));?>">
                        <?php _e('Lengthy operation in progress:','shortpixel-image-optimiser');?><br>
                        <?php _e('Optimizing image','shortpixel-image-optimiser');?> <a href="#" data-href="<?php echo(get_admin_url());?>/post.php?post=__ID__&action=edit" target="_blank">placeholder.png</a>
                    </div>
                    <div class="bulk-notice-msg bulk-maintenance">
                        <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/loading-dark-big.gif' ));?>">
                        <?php _e("The ShortPixel API is in maintenance mode. Please don't close this window. The bulk will resume automatically as soon as the API is back online.",'shortpixel-image-optimiser');?>
                    </div>
                    <div class="bulk-notice-msg bulk-queue-full">
                        <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/loading-dark-big.gif' ));?>">
                        <?php _e("Too many images processing simultaneously for your site, automatically retrying in 1 min. Please don't close this window.",'shortpixel-image-optimiser');?>
                    </div>
                    <div class="bulk-notice-msg bulk-error" id="bulk-error-template">
                        <div style="float: right; margin-top: -4px; margin-right: -8px;">
                            <a href="javascript:void(0);" onclick="ShortPixel.removeBulkMsg(this)" style='color: #c32525;'>&#10006;</a>
                        </div>
                        <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/exclamation-big.png' ));?>">
                        <span class="sp-err-title"><?php _e('Error processing file:','shortpixel-image-optimiser');?><br></span>
                        <span class="sp-err-content"><?php echo $message; ?></span> <a class="sp-post-link" href="<?php echo(get_admin_url());?>/post.php?post=__ID__&action=edit" target="_blank">placeholder.png</a>
                    </div>
                </div>
            </div>
            <div class="bulk-progress bulk-slider-container sp-notice sp-notice-info sp-floating-block sp-full-width">
                <div  class="short-pixel-block-title"><span><?php _e('Just optimized:','shortpixel-image-optimiser');?></span><span class="filename"></span></div>
                <div class="bulk-slider">
                    <div class="bulk-slide" id="empty-slide">
                        <div class="bulk-slide-images">
                            <div class="img-original">
                                <div><img class="bulk-img-orig" src=""></div>
                              <div><?php _e('Original image','shortpixel-image-optimiser');?></div>
                            </div>
                            <div class="img-optimized">
                                <div><img class="bulk-img-opt" src=""></div>
                              <div><?php _e('Optimized image','shortpixel-image-optimiser');?></div>
                            </div>
                        </div>
                        <div class="img-info">
                            <div style="font-size: 14px; line-height: 10px; margin-bottom:16px;"><?php /*translators: percent follows */ _e('Optimized by:','shortpixel-image-optimiser');?></div>
                            <span class="bulk-opt-percent"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function displayBulkProgressBar($running, $percent, $message, $remainingQuota, $averageCompression, $type = 1, $customPending = false) {
        $percentBefore = $percentAfter = '';
        if($percent > 24) {
            $percentBefore = $percent . "%";
        } else {
            $percentAfter = $percent . "%";
        }
        ?>
            <div class="sp-notice sp-notice-info bulk-progress sp-floating-block sp-full-width">
                <div style="float:right">
                    <?php if(false) { ?>
                    <div class="bulk-progress-indicator">
                        <div style="margin-bottom:5px"><?php _e('Remaining credits','shortpixel-image-optimiser');?></div>
                        <div style="margin-top:22px;margin-bottom: 5px;font-size:2em;font-weight: bold;"><?php echo(number_format($remainingQuota))?></div>
                        <div>images</div>
                    </div>
                    <?php } ?>
                    <div class="bulk-progress-indicator">
                        <div style="margin-bottom:5px"><?php _e('Average reduction','shortpixel-image-optimiser');?></div>
                        <div id="sp-avg-optimization"><input type="text" id="sp-avg-optimization-dial" value="<?php echo("" . round($averageCompression))?>" class="dial"></div>
                        <script>
                            jQuery(function() {
                                ShortPixel.percentDial("#sp-avg-optimization-dial", 60);
                            });
                        </script>
                    </div>
                </div>
                <?php if($running) { 
                    if($type > 0) { ?>
                    <div class="sp-h2"><?php 
                              echo($type & 1 ? __('Media Library','shortpixel-image-optimiser') . " " : "");
                              echo($type & 3 == 3 ? __('and','shortpixel-image-optimiser') . " " : "");
                              echo($type & 2 ? __('Custom folders','shortpixel-image-optimiser') . " " : ""); 
                              _e('optimization in progress ...','shortpixel-image-optimiser');?></div>
                    <p style="margin: 0 0 18px;"><?php _e('Bulk optimization has started.','shortpixel-image-optimiser');?><br>
                    <?php 
                    } elseif($type == 0) { // restore ?>
                <div class="sp-h2"><?php 
                        _e('Media Library restore in progress ...','shortpixel-image-optimiser');?></div>
                        <p style="margin: 0 0 18px;"><?php _e('Bulk restore has started.','shortpixel-image-optimiser');?><br>                                        
                    <?php }
                    elseif($type == -1) { // cleanup ?>
                <div class="sp-h2"><?php 
                        _e('Media Library cleanup in progress ...','shortpixel-image-optimiser');?></div>
                        <p style="margin: 0 0 18px;"><?php _e('Bulk cleanup has started.','shortpixel-image-optimiser');?><br>                                        
                    <?php }
                    printf(__('This process will take some time, depending on the number of images in your library. In the meantime, you can continue using 
                    the admin as usual, <a href="%s" target="_blank">in a different browser window or tab</a>.<br>
                   However, <strong>if you close this window, the bulk processing will pause</strong> until you open the media gallery or the ShortPixel bulk page again.','shortpixel-image-optimiser'), get_admin_url());?>
                </p>
                <?php } else { ?>
                <div class="sp-h2"><?php echo(__('Media Library','shortpixel-image-optimiser') . ' ' . ($type & 2 ? __("and Custom folders",'shortpixel-image-optimiser') . ' ' : "") . __('optimization paused','shortpixel-image-optimiser')); ?></div>
                <p style="margin: 0 0 50px;"><?php _e('Bulk processing is paused until you resume the optimization process.','shortpixel-image-optimiser');?></p>
                <?php }?>
                <div id="bulk-progress" class="progress" >
                    <div class="progress-img" style="left: <?php echo($percent);?>%;">
                        <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/slider.png' ));?>"
                             srcset='<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/slider.png' ));?> 1x, <?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/slider@2x.png' ));?> 2x'>
                        <span><?php echo($percentAfter);?></span>
                    </div>
                    <div class="progress-left" style="width: <?php echo($percent);?>%"><?php echo($percentBefore);?></div>
                </div>
                <div class="bulk-estimate">
                    &nbsp;<?php echo($message);?>
                </div>
                <?php if (true || ($type & 1)) { //now we display the action buttons always when a type of bulk is running ?>
                <form action='' method='POST' style="display:inline;">
                    <input type="submit" class="button button-primary bulk-cancel"  onclick="clearBulkProcessor();"
                           name="bulkProcessStop" value="Stop" style="margin-left:10px"/>
                    <input type="submit" class="button button-primary bulk-cancel"  onclick="clearBulkProcessor();"
                           name="<?php echo($running ? "bulkProcessPause" : "bulkProcessResume");?>" value="<?php echo($running ? __('Pause','shortpixel-image-optimiser') : __('Resume processing','shortpixel-image-optimiser'));?>"/>
                    <?php if(!$running && $customPending) {?>
                        <input type="submit" class="button button-primary bulk-cancel"  onclick="clearBulkProcessor();"
                               name="skipToCustom" value="<?php _e('Only other media','shortpixel-image-optimiser');?>" title="<?php _e('Process only the other media, skipping the Media Library','shortpixel-image-optimiser');?>" style="margin-right:10px"/>
                    <?php }?>
                </form>
                <?php } else { ?>
                    <a href="options-general.php?page=wp-shortpixel" class="button button-primary bulk-cancel" style="margin-left:10px"><?php _e('Manage custom folders','shortpixel-image-optimiser');?></a>
                <?php }?>
            </div>
        <?php
    }
    
    public function displayBulkStats($totalOptimized, $mainOptimized, $under5PercentCount, $averageCompression, $savedSpace) {?>
            <div class="bulk-progress bulk-stats">
                <div class="label"><?php _e('Processed Images and PDFs:','shortpixel-image-optimiser');?></div><div class="stat-value"><?php echo(number_format($mainOptimized));?></div><br>
                <div class="label"><?php _e('Processed Thumbnails:','shortpixel-image-optimiser');?></div><div class="stat-value"><?php echo(number_format($totalOptimized - $mainOptimized));?></div><br>
                <div class="label totals"><?php _e('Total files processed:','shortpixel-image-optimiser');?></div><div class="stat-value"><?php echo(number_format($totalOptimized));?></div><br>
                <div class="label totals"><?php _e('Minus files with <5% optimization (free):','shortpixel-image-optimiser');?></div><div class="stat-value"><?php echo(number_format($under5PercentCount));?></div><br><br>
                <div class="label totals"><?php _e('Used quota:','shortpixel-image-optimiser');?></div><div class="stat-value"><?php echo(number_format($totalOptimized - $under5PercentCount));?></div><br>
                <br>
                <div class="label"><?php _e('Average optimization:','shortpixel-image-optimiser');?></div><div class="stat-value"><?php echo($averageCompression);?>%</div><br>
                <div class="label"><?php _e('Saved space:','shortpixel-image-optimiser');?></div><div class="stat-value"><?php echo($savedSpace);?></div>
            </div>
        <?php
    }
     
    public function displayFailed($failed) {
        ?>
            <div class="bulk-progress bulk-stats">
                <?php foreach($failed as $fail) { 
                    if($fail->type == ShortPixelMetaFacade::CUSTOM_TYPE) {
                        $meta = $fail->meta;
                        ?> <div class="label"><a href="<?php echo(ShortPixelMetaFacade::getHomeUrl() . $fail->meta->getWebPath());?>"><?php echo(substr($fail->meta->getName(), 0, 80));?> - ID: C-<?php echo($fail->id);?></a></div><br/>
                    <?php } else {
                        $meta = wp_get_attachment_metadata($fail);
                        ?> <div class="label"><a href="/wp-admin/post.php?post=<?php echo($fail->id);?>&action=edit"><?php echo(substr($fail->meta["file"], 0, 80));?> - ID: <?php echo($fail->id);?></a></div><br/>
                    <?php }
                }?>
            </div>
        <?php
    }

    function displaySettings($showApiKey, $editApiKey, $quotaData, $notice, $resources = null, $averageCompression = null, $savedSpace = null, $savedBandwidth = null, 
                         $remainingImages = null, $totalCallsMade = null, $fileCount = null, $backupFolderSize = null, 
                         $customFolders = null, $folderMsg = false, $addedFolder = false, $showAdvanced = false) { 
        //wp_enqueue_script('jquery.idTabs.js', plugins_url('/js/jquery.idTabs.js',__FILE__) );
        ?>        
        <h1><?php _e('ShortPixel Plugin Settings','shortpixel-image-optimiser');?></h1>
        <p style="font-size:18px">
            <a href="https://shortpixel.com/<?php 
            echo($this->ctrl->getVerifiedKey() ? "login/".(defined("SHORTPIXEL_HIDE_API_KEY") ? '' : $this->ctrl->getApiKey()) : "pricing");
            ?>" target="_blank" style="font-size:18px">
                <?php _e('Upgrade now','shortpixel-image-optimiser');?>
            </a> | <a href="https://shortpixel.com/pricing#faq" target="_blank" style="font-size:18px"><?php _e('FAQ','shortpixel-image-optimiser');?> </a> | 
            <a href="https://shortpixel.com/contact/<?php //echo($this->ctrl->getEncryptedData());?>" target="_blank" style="font-size:18px"><?php _e('Support','shortpixel-image-optimiser');?> </a>
        </p>
        <?php if($notice !== null) { ?>
        <br/>
        <div style="background-color: #fff; border-left: 4px solid <?php echo($notice['status'] == 'error' ? '#ff0000' : ($notice['status'] == 'warn' ? '#FFC800' : '#7ad03a'));?>; box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1); padding: 1px 12px;;width: 95%">
                  <p><?php echo($notice['msg']);?></p>
        </div>
        <?php } ?>
        <?php if($folderMsg) { ?>
        <br/>
        <div style="background-color: #fff; border-left: 4px solid #ff0000; box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1); padding: 1px 12px;;width: 95%">
                  <p><?php echo($folderMsg);?></p>
        </div>
        <?php } ?>

        <article id="shortpixel-settings-tabs" class="sp-tabs">
            <form name='wp_shortpixel_options' action='options-general.php?page=wp-shortpixel&noheader=true'  method='post' id='wp_shortpixel_options'>
                <section <?php echo($showAdvanced ? "" : "class='sel-tab'");?> id="tab-settings">
                    <h2><a class='tab-link' href='javascript:void(0);' data-id="tab-settings"><?php _e('General','shortpixel-image-optimiser');?></a></h2>
                    <?php $this->displaySettingsForm($showApiKey, $editApiKey, $quotaData);?>
                </section> 
                <?php if($this->ctrl->getVerifiedKey()) {?>
                <section <?php echo($showAdvanced ? "class='sel-tab'" : "");?> id="tab-adv-settings">
                    <h2><a class='tab-link' href='javascript:void(0);' data-id="tab-adv-settings"><?php _e('Advanced','shortpixel-image-optimiser');?></a></h2>
                    <?php $this->displayAdvancedSettingsForm($customFolders, $addedFolder);?>
                </section>
                <?php } ?>
            </form><span style="display:none">&nbsp;</span><?php //the span is a trick to keep the sections ordered as nth-child in styles: 1,2,3,4 (otherwise the third section would be nth-child(2) too, because of the form)
            if($averageCompression !== null) {?>
            <section id="tab-stats">
                <h2><a class='tab-link' href='javascript:void(0);' data-id="tab-stats"><?php _e('Statistics','shortpixel-image-optimiser');?></a></h2>
                <?php
                    $this->displaySettingsStats($quotaData, $averageCompression, $savedSpace, $savedBandwidth, 
                                                $remainingImages, $totalCallsMade, $fileCount, $backupFolderSize);?>
            </section> 
            <?php }
            if($resources !== null) {?>
            <section id="tab-resources">
		        <h2><a class='tab-link' href='javascript:void(0);' data-id="tab-resources"><?php _e('WP Resources','shortpixel-image-optimiser');?></a></h2>
                <?php echo((isset($resources['body']) ? $resources['body'] : __("Please reload",'shortpixel-image-optimiser')));?>
            </section>
            <?php } ?>
        </article>
        <script>
            jQuery(document).ready(function () {
                ShortPixel.adjustSettingsTabs();
                jQuery( window ).resize(function() {
                    ShortPixel.adjustSettingsTabs();
                });
                if(window.location.hash) {
                    var target = 'tab-' + window.location.hash.substring(window.location.hash.indexOf("#")+1)
                    ShortPixel.switchSettingsTab(target);
                }
                jQuery("article.sp-tabs a.tab-link").click(function(){ShortPixel.switchSettingsTab(jQuery(this).data("id"))});
            });
        </script>
        <?php
    }    
    
    public function displaySettingsForm($showApiKey, $editApiKey, $quotaData) {
        $settings = $this->ctrl->getSettings();
        $checked = ($this->ctrl->processThumbnails() ? 'checked' : '');
        $checkedBackupImages = ($this->ctrl->backupImages() ? 'checked' : '');
        $cmyk2rgb = ($this->ctrl->getCMYKtoRGBconversion() ? 'checked' : '');
        $removeExif = ($settings->keepExif ? '' : 'checked');
        $resize = ($this->ctrl->getResizeImages() ? 'checked' : '');
        $resizeDisabled = ($this->ctrl->getResizeImages() ? '' : 'disabled');        
        $minSizes = $this->ctrl->getMaxIntermediateImageSize();
        $thumbnailsToProcess = isset($quotaData['totalFiles']) ? ($quotaData['totalFiles'] - $quotaData['mainFiles']) - ($quotaData['totalProcessedFiles'] - $quotaData['mainProcessedFiles']) : 0;
        ?>
        <div class="wp-shortpixel-options">
        <?php if($this->ctrl->getVerifiedKey()) { ?>
            <p><?php printf(__('New images uploaded to the Media Library will be optimized automatically.<br/>If you have existing images you would like to optimize, you can use the <a href="%supload.php?page=wp-short-pixel-bulk">Bulk Optimization Tool</a>.','shortpixel-image-optimiser'),get_admin_url());?></p>
        <?php } else { 
            if($showApiKey) {?>
            <h3><?php _e('Step 1:','shortpixel-image-optimiser');?></h3>
            <p style='font-size: 14px'><?php _e('If you don\'t have an API Key, you can request one for free. Just press the "Request Key" button after checking that the e-mail is correct.','shortpixel-image-optimiser');?></p>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="key"><?php _e('E-mail address:','shortpixel-image-optimiser');?></label></th>
                        <td>
                            <input name="pluginemail" type="text" id="pluginemail" value="<?php echo( get_bloginfo('admin_email') );?>" 
                                   onchange="ShortPixel.updateSignupEmail();" class="regular-text">
                            <a type="button" id="request_key" class="button button-primary" title="<?php _e('Request a new API key','shortpixel-image-optimiser');?>"
                               href="https://shortpixel.com/free-sign-up?pluginemail=<?php echo( get_bloginfo('admin_email') );?>" 
                               onmouseenter="ShortPixel.updateSignupEmail();" target="_blank">
                               <?php _e('Request Key','shortpixel-image-optimiser');?>
                            </a>
                            <p class="settings-info">
                                <?php printf(__('<b>%s</b> is the e-mail address in your WordPress Settings. You can use it, or change it to any valid e-mail address that you own.','shortpixel-image-optimiser'), get_bloginfo('admin_email'));?>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3><?php _e('Step 2:','shortpixel-image-optimiser');?></h3>
            <p style='font-size: 14px'><?php _e('Please enter here the API Key you received by email and press Validate.','shortpixel-image-optimiser');?></p>
            <?php } 
        }?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="key"><?php _e('API Key:','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <?php 
                        $canValidate = false;
                        if($showApiKey) {
                            $canValidate = true;?>
                            <input name="key" type="text" id="key" value="<?php echo( $this->ctrl->getApiKey() );?>" 
                               class="regular-text" <?php echo($editApiKey ? "" : 'disabled') ?>>
                        <?php } elseif(defined("SHORTPIXEL_API_KEY")) { 
                            $canValidate = true;?>
                            <input name="key" type="text" id="key" disabled="true" placeholder="<?php 
                            if(defined("SHORTPIXEL_HIDE_API_KEY")) {
                                echo("********************");                              
                            } else {
                                _e('Multisite API Key','shortpixel-image-optimiser');
                            }
                            ?>" class="regular-text">
                        <?php } ?>
                            <input type="hidden" name="validate" id="valid" value=""/>
                            <button type="button" id="validate" class="button button-primary" title="<?php _e('Validate the provided API key','shortpixel-image-optimiser');?>"
                                onclick="ShortPixel.validateKey()" <?php echo $canValidate ? "" : "disabled"?>><?php _e('Validate','shortpixel-image-optimiser');?></button>
                        <?php if($showApiKey && !$editApiKey) { ?>
                            <p class="settings-info"><?php _e('Key defined in wp-config.php.','shortpixel-image-optimiser');?></p>
                        <?php } ?>
                        
                    </td>
                </tr>
        <?php if (!$this->ctrl->getVerifiedKey()) { //if invalid key we display the link to the API Key ?>
            </tbody>
        </table>
        <?php } else { //if valid key we display the rest of the options ?>
                <tr>
                    <th scope="row">
                        <label for="compressionType"><?php _e('Compression type:','shortpixel-image-optimiser');?></label>
                    </th>
                    <td>
                        <input type="radio" name="compressionType" value="1" <?php echo( $this->ctrl->getCompressionType() == 1 ? "checked" : "" );?>><?php 
                            _e('Lossy (recommended)','shortpixel-image-optimiser');?></br>
                        <p class="settings-info"><?php _e('<b>Lossy compression: </b>offers the best compression rate.</br> This is the recommended option for most users, producing results that look the same as the original to the human eye. You can run a test for free ','shortpixel-image-optimiser');?>
                            <a href="https://shortpixel.com/online-image-compression" target="_blank"><?php _e('here','shortpixel-image-optimiser');?></a>.</p></br>
                        <input type="radio" name="compressionType" value="2" <?php echo( $this->ctrl->getCompressionType() == 2 ? "checked" : "" );?>><?php 
                            _e('Glossy','shortpixel-image-optimiser');?></br>
                        <p class="settings-info"><?php _e('<b>Glossy compression: </b>creates images that are almost pixel-perfect identical to the originals.</br> Best option for photographers and other professionals that use very high quality images on their sites and want best compression while keeping the quality untouched.','shortpixel-image-optimiser');?>
                        </p></br>
                        <input type="radio" name="compressionType" value="0" <?php echo( $this->ctrl->getCompressionType() == 0 ? "checked" : "" );?>><?php 
                            _e('Lossless','shortpixel-image-optimiser');?>
                        <p class="settings-info">
                            <?php _e('<b>Lossless compression: </b> the resulting image is pixel-identical with the original image.</br>Make sure not a single pixel looks different in the optimized image compared with the original. 
                            In some rare cases you will need to use this type of compression. Some technical drawings or images from vector graphics are possible situations.','shortpixel-image-optimiser');?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="thumbnails"><?php _e('Also include thumbnails:','shortpixel-image-optimiser');?></label></th>
                    <td><input name="thumbnails" type="checkbox" id="thumbnails" <?php echo( $checked );?>> <?php 
                            _e('Apply compression also to <strong>image thumbnails.</strong> ','shortpixel-image-optimiser');?>
                            <?php echo($thumbnailsToProcess ? "(" . number_format($thumbnailsToProcess) . " " . __('thumbnails to optimize','shortpixel-image-optimiser') . ")" : "");?>
                        <p class="settings-info">
                            <?php _e('It is highly recommended that you optimize the thumbnails as they are usually the images most viewed by end users and can generate most traffic.<br>Please note that thumbnails count up to your total quota.','shortpixel-image-optimiser');?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="backupImages"><?php _e('Image backup','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="backupImages" type="checkbox" id="backupImages" <?php echo( $checkedBackupImages );?>> <?php _e('Save and keep a backup of your original images in a separate folder.','shortpixel-image-optimiser');?>
                        <p class="settings-info"><?php _e('You <strong>need to have backup active</strong> in order to be able to restore images to originals or to convert from Lossy to Lossless and back.','shortpixel-image-optimiser');?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="cmyk2rgb"><?php _e('CMYK to RGB conversion','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="cmyk2rgb" type="checkbox" id="cmyk2rgb" <?php echo( $cmyk2rgb );?>><?php _e('Adjust your images for computer and mobile screen display.','shortpixel-image-optimiser');?>
                        <p class="settings-info"><?php _e('Images for the web only need RGB format and converting them from CMYK to RGB makes them smaller.','shortpixel-image-optimiser');?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="removeExif"><?php _e('Remove EXIF','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="removeExif" type="checkbox" id="removeExif" <?php echo( $removeExif );?>><?php _e('Remove the EXIF tag of the image (recommended).','shortpixel-image-optimiser');?>
                        <p class="settings-info"> <?php _e('EXIF is a set of various pieces of information that are automatically embedded into the image upon creation. This can include GPS position, camera manufacturer, date and time, etc.  
                            Unless you really need that data to be preserved, we recommend removing it as it can lead to <a href="http://blog.shortpixel.com/how-much-smaller-can-be-images-without-exif-icc" target="_blank">better compression rates</a>.','shortpixel-image-optimiser');?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="resize"><?php _e('Resize large images','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="resize" type="checkbox" id="resize" <?php echo( $resize );?>> <?php 
                               _e('to maximum','shortpixel-image-optimiser');?> <input type="text" name="width" id="width" style="width:70px" class="resize-sizes" 
                               value="<?php echo( $this->ctrl->getResizeWidth() > 0 ? $this->ctrl->getResizeWidth() : min(1024, $minSizes['width']) );?>" <?php echo( $resizeDisabled );?>/> <?php 
                               _e('pixels wide &times;','shortpixel-image-optimiser');?>
                        <input type="text" name="height" id="height" class="resize-sizes" style="width:70px" 
                               value="<?php echo( $this->ctrl->getResizeHeight() > 0 ? $this->ctrl->getResizeHeight() : min(1024, $minSizes['height']) );?>" <?php echo( $resizeDisabled );?>/> <?php 
                               _e('pixels high (original aspect ratio is preserved and image is not cropped)','shortpixel-image-optimiser');?>
                        <input type="hidden" id="min-width" value="<?php echo($minSizes['width']);?>"/>
                        <input type="hidden" id="min-height" value="<?php echo($minSizes['height']);?>"/>
                        <p class="settings-info"> 
                            <?php _e('Recommended for large photos, like the ones taken with your phone. Saved space can go up to 80% or more after resizing.','shortpixel-image-optimiser');?><br/>
                        </p>
                        <div style="margin-top: 10px;">
                            <input type="radio" name="resize_type" id="resize_type_outer" value="outer" <?php echo($settings->resizeType == 'inner' ? '' : 'checked') ?> style="margin: -50px 10px 60px 0;">
                            <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/resize-outer.png' ));?>" title="<?php _e('Sizes will be greater or equal to the corresponding value. For example, if you set the resize dimensions at 1000x1200, an image of 2000x3000px will be resized to 1000x1500px while an image of 3000x2000px will be resized to 1800x1200px','shortpixel-image-optimiser');?>">
                            <input type="radio" name="resize_type" id="resize_type_inner" value="inner" <?php echo($settings->resizeType == 'inner' ? 'checked' : '') ?> style="margin: -50px 10px 60px 35px;">
                            <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/resize-inner.png' ));?>" title="<?php _e('Sizes will be smaller or equal to the corresponding value. For example, if you set the resize dimensions at 1000x1200, an image of 2000x3000px will be resized to 800x1200px while an image of 3000x2000px will be resized to 1000x667px','shortpixel-image-optimiser');?>">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="save" id="save" class="button button-primary" title="<?php _e('Save Changes','shortpixel-image-optimiser');?>" value="<?php _e('Save Changes','shortpixel-image-optimiser');?>"> &nbsp;
            <input type="submit" name="save" id="bulk" class="button button-primary" title="<?php _e('Save and go to the Bulk Processing page','shortpixel-image-optimiser');?>" value="<?php _e('Save and Go to Bulk Process','shortpixel-image-optimiser');?>"> &nbsp;
        </p>
        </div>
        <script>
            jQuery(document).ready(function () {
                ShortPixel.setupGeneralTab(document.wp_shortpixel_options.compressionType, 
                                       Math.min(1024, <?php echo($minSizes['width']);?>),
                                       Math.min(1024, <?php echo($minSizes['height']);?>));
            });
        </script>
        <?php }
    }
    
    public function displayAdvancedSettingsForm($customFolders = false, $addedFolder = false) {
        $settings = $this->ctrl->getSettings();
        $minSizes = $this->ctrl->getMaxIntermediateImageSize();
        $hasNextGen = $this->ctrl->hasNextGen();
        $frontBootstrap = ($settings->frontBootstrap ? 'checked' : '');
        $includeNextGen = ($settings->includeNextGen ? 'checked' : '');
        $createWebp = ($settings->createWebp ? 'checked' : '');
        $createWebpMarkup = ($settings->createWebpMarkup ? 'checked' : '');
        $autoMediaLibrary = ($settings->autoMediaLibrary ? 'checked' : '');
        $optimizeRetina = ($settings->optimizeRetina ? 'checked' : '');
        $optimizePdfs = ($settings->optimizePdfs ? 'checked' : '');
        $excludePatterns = "";
        if($settings->excludePatterns) {
            foreach($settings->excludePatterns as $item) {
                $excludePatterns .= $item['type'] . ":" . $item['value'] . ", ";
            }
            $excludePatterns = substr($excludePatterns, 0, -2);
        }
        $convertPng2Jpg = ($settings->png2jpg ? 'checked' : '');
        ?>
        <div class="wp-shortpixel-options">
        <?php if(!$this->ctrl->getVerifiedKey()) { ?>
            <p><?php _e('Please enter your API key in the General tab first.','shortpixel-image-optimiser');?></p>
        <?php } else { //if valid key we display the rest of the options ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="resize"><?php _e('Additional media folders','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <?php if($customFolders) { ?>
                            <table class="shortpixel-folders-list">
                                <tr style="font-weight: bold;">
                                    <td><?php _e('Folder name','shortpixel-image-optimiser');?></td>
                                    <td><?php _e('Type &amp;<br>Status','shortpixel-image-optimiser');?></td>
                                    <td><?php _e('Files','shortpixel-image-optimiser');?></td>
                                    <td><?php _e('Last change','shortpixel-image-optimiser');?></td>
                                    <td></td>
                                </tr>
                            <?php foreach($customFolders as $folder) {
                                $typ = $folder->getType(); 
                                $typ = $typ ? $typ . "<br>" : "";
                                $stat = $this->ctrl->getSpMetaDao()->getFolderOptimizationStatus($folder->getId());
                                $cnt = $folder->getFileCount();
                                $st = ($cnt == 0 
                                    ? __("Empty",'shortpixel-image-optimiser')
                                    : ($stat->Total == $stat->Optimized 
                                        ? __("Optimized",'shortpixel-image-optimiser')
                                        : ($stat->Optimized + $stat->Pending > 0 ? __("Pending",'shortpixel-image-optimiser') : __("Waiting",'shortpixel-image-optimiser'))));
                                
                                $err = $stat->Failed > 0 && !$st == __("Empty",'shortpixel-image-optimiser') ? " ({$stat->Failed} failed)" : "";
                                
                                $action = ($st == __("Optimized",'shortpixel-image-optimiser') || $st == __("Empty",'shortpixel-image-optimiser') ? __("Stop monitoring",'shortpixel-image-optimiser') : __("Stop optimizing",'shortpixel-image-optimiser'));
                                
                                $fullStat = $st == __("Empty",'shortpixel-image-optimiser') ? "" : __("Optimized",'shortpixel-image-optimiser') . ": " . $stat->Optimized . ", " 
                                        . __("Pending",'shortpixel-image-optimiser') . ": " . $stat->Pending . ", " . __("Waiting",'shortpixel-image-optimiser') . ": " . $stat->Waiting . ", " 
                                        . __("Failed",'shortpixel-image-optimiser') . ": " . $stat->Failed;
                                ?>
                                <tr>
                                    <td>
                                        <?php echo($folder->getPath()); ?>
                                    </td>
                                    <td>
                                        <?php if(!($st == "Empty")) { ?>
                                        <a href="javascript:none();"  title="<?php echo $fullStat; ?>" style="text-decoration: none;">
                                            <img src='<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/info-icon.png' ));?>' style="margin-bottom: -2px;"/>
                                        </a>&nbsp;<?php  } echo($typ.$st.$err); ?>

                                    </td>
                                    <td>
                                        <?php echo($cnt); ?> files
                                    </td>
                                    <td>
                                        <?php echo($folder->getTsUpdated()); ?>
                                    </td>
                                    <td>
                                        <input type="button" class="button remove-folder-button" data-value="<?php echo($folder->getPath()); ?>" title="<?php echo($action . " " . $folder->getPath()); ?>" value="<?php echo $action;?>">
                                        <input type="button" style="display:none;" class="button button-alert recheck-folder-button" data-value="<?php echo($folder->getPath()); ?>" 
                                               title="<?php _e('Full folder refresh, check each file of the folder if it changed since it was optimized. Might take up to 1 min. for big folders.','shortpixel-image-optimiser');?>" 
                                               value="<?php _e('Refresh','shortpixel-image-optimiser');?>">
                                    </td>
                                </tr>
                            <?php }?>
                            </table>
                        <?php } ?>
                        <input type="hidden" name="removeFolder" id="removeFolder"/>
                        <input type="hidden" name="recheckFolder" id="removeFolder"/>
                        <input type="text" name="addCustomFolderView" id="addCustomFolderView" class="regular-text" value="<?php echo($addedFolder);?>" disabled style="width: 50em;max-width: 70%;">&nbsp;
                        <input type="hidden" name="addCustomFolder" id="addCustomFolder" value="<?php echo($addedFolder);?>"/>
                        <input type="hidden" id="customFolderBase" value="<?php echo WPShortPixel::getCustomFolderBase(); ?>">
                        <a class="button button-primary select-folder-button" title="<?php _e('Select the images folder on your server.','shortpixel-image-optimiser');?>" href="javascript:void(0);">
                            <?php _e('Select ...','shortpixel-image-optimiser');?> 
                        </a>
                        <input type="submit" name="saveAdv" id="saveAdvAddFolder" class="button button-primary" title="<?php _e('Add Folder','shortpixel-image-optimiser');?>" value="<?php _e('Add Folder','shortpixel-image-optimiser');?>">
                        <p class="settings-info">
                            <?php _e('Use the Select... button to select site folders. ShortPixel will optimize images and PDFs from the specified folders and their subfolders. The optimization status for each image or PDF in these folders can be seen in the <a href="upload.php?page=wp-short-pixel-custom">Other Media list</a>, under the Media menu.','shortpixel-image-optimiser');?>
                        </p>
                        <div class="sp-modal-shade sp-folder-picker-shade">
                            <div class="shortpixel-modal">
                                <div class="sp-modal-title"><?php _e('Select the images folder','shortpixel-image-optimiser');?></div>
                                <div class="sp-folder-picker"></div>
                                <input type="button" class="button button-info select-folder-cancel" value="<?php _e('Cancel','shortpixel-image-optimiser');?>" style="margin-right: 30px;">
                                <input type="button" class="button button-primary select-folder" value="<?php _e('Select','shortpixel-image-optimiser');?>">
                            </div>
                        </div>
                        <script>
                            jQuery(document).ready(function () {
                                ShortPixel.initFolderSelector();
                            });
                        </script>
                    </td>
                </tr>
                <?php if($hasNextGen) { ?>
                <tr>
                    <th scope="row"><label for="nextGen"><?php _e('Optimize NextGen galleries','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="nextGen" type="checkbox" id="nextGen" <?php echo( $includeNextGen );?>> <?php _e('Optimize NextGen galleries.','shortpixel-image-optimiser');?>
                        <p class="settings-info">
                            <?php _e('Check this to add all your current NextGen galleries to the custom folders list and to also have all the future NextGen galleries and images optimized automatically by ShortPixel.','shortpixel-image-optimiser');?>
                        </p>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row"><label for="png2jpg"><?php _e('Convert PNG images to JPEG','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="png2jpg" type="checkbox" id="resize" <?php echo( $convertPng2Jpg );?>> <?php _e('Automatically convert the PNG images to JPEG if possible.','shortpixel-image-optimiser');?>
                        <p class="settings-info">
                            <?php _e('Converts all PNGs that don\'t have transparent pixels to JPEG. This can dramatically reduce the file size, especially if you have pictures that are saved in PNG format. <strong>It currently works only on new media uploaded and provides no backup.</strong>','shortpixel-image-optimiser');?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="createWebp"><?php _e('WebP versions','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="createWebp" type="checkbox" id="createWebp" <?php echo( $createWebp );?>> <?php _e('Create also <a href="http://blog.shortpixel.com/how-webp-images-can-speed-up-your-site/" target="_blank">WebP versions</a> of the images <strong>for free</strong>.','shortpixel-image-optimiser');?>
                        <p class="settings-info">
                            <?php _e('WebP images can be up to three times smaller than PNGs and 25% smaller than JPGs. Choosing this option <strong>does not use up additional credits</strong>.','shortpixel-image-optimiser');?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="createWebpMarkup"><?php _e('Generate WebP markup','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="createWebpMarkup" type="checkbox" id="createWebpMarkup" <?php echo( $createWebpMarkup );?>> <?php _e('Generate the &lt;picture&gt; markup in the front-end.','shortpixel-image-optimiser');?>
                        <p class="settings-info">
                            <?php _e('Each &lt;img&gt; will be replaced with a &lt;picture&gt; tag that will also provide the WebP image as a choice for browsers that support it. Also loads the picturefill.js for browsers that don\'t support the &lt;picture&gt; tag. You don\'t need to activate this if you\'re using the Cache Enabler plugin because your WebP images are already handled by this plugin. <strong>Please make a test before using this option</strong>, as if the styles that your theme is using rely on the position of your &lt;img&gt; tag, you might experience display problems.','shortpixel-image-optimiser');?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="optimizeRetina"><?php _e('Optimize Retina images','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="optimizeRetina" type="checkbox" id="optimizeRetina" <?php echo( $optimizeRetina );?>> <?php _e('Optimize also the Retina images (@2x) if they exist.','shortpixel-image-optimiser');?>
                        <p class="settings-info">
                            <?php _e('If you have a Retina plugin that generates Retina-specific images (@2x), ShortPixel can optimize them too, alongside the regular Media Library images and thumbnails. <a href="http://blog.shortpixel.com/how-to-use-optimized-retina-images-on-your-wordpress-site-for-best-user-experience-on-apple-devices/" target="_blank">More info.</a>','shortpixel-image-optimiser');?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="optimizePdfs"><?php _e('Optimize PDFs','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="optimizePdfs" type="checkbox" id="optimizePdfs" <?php echo( $optimizePdfs );?>> <?php _e('Automatically optimize PDF documents.','shortpixel-image-optimiser');?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="excludePatterns"><?php _e('Exclude patterns','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="excludePatterns" type="text" id="excludePatterns" value="<?php echo( $excludePatterns );?>" class="regular-text" placeholder="<?php 
                            _e('name:keepbig, path:/ignore_regex/i, size:1000x2000','shortpixel-image-optimiser');?>"> 
                        <?php _e('Exclude certain images from being optimized, based on patterns.','shortpixel-image-optimiser');?>
                        <p class="settings-info"> 
                            <?php _e('Add patterns separated by comma. A pattern consist of a <strong>type:value</strong> pair; the accepted types are '
                                    . '<strong>"name"</strong>, <strong>"path"</strong> and <strong>"size"</strong>. '
                                    . 'A file will be excluded if it matches any of the patterns. '
                                    . '<br>For a <strong>"name"</strong> pattern only the filename will be matched but for a <strong>"path"</strong>, '
                                    . 'all the path will be matched (useful for excluding certain subdirectories altoghether).'
                                    . 'For these you can also use regular expressions accepted by preg_match, but without "," or ":". '
                                    . 'A pattern will be considered a regex if it starts with a "/" and is valid. '
                                    . '<br>For the <strong>"size"</strong> type, '
                                    . 'which applies only to Media Library images, <strong>the main images (not thumbnails)</strong> that have the size in the specified range will be excluded. '
                                    . 'The format for the "size" exclude is: <strong>minWidth</strong>-<strong>maxWidth</strong>x<strong>minHeight</strong>-<strong>maxHeight</strong>, for example <strong>size:1000-1100x2000-2200</strong>. You can also specify a precise size, as <strong>1000x2000</strong>.','shortpixel-image-optimiser');?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="authentication"><?php _e('HTTP AUTH credentials','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="siteAuthUser" type="text" id="siteAuthUser" value="<?php echo( $settings->siteAuthUser );?>" class="regular-text" placeholder="<?php _e('User','shortpixel-image-optimiser');?>"><br>
                        <input name="siteAuthPass" type="text" id="siteAuthPass" value="<?php echo( $settings->siteAuthPass );?>" class="regular-text" placeholder="<?php _e('Password','shortpixel-image-optimiser');?>">
                        <p class="settings-info"> 
                            <?php _e('Only fill in these fields if your site (front-end) is not publicly accessible and visitors need a user/pass to connect to it. If you don\'t know what is this then just <strong>leave the fields empty</strong>.','shortpixel-image-optimiser');?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="resize"><?php _e('Process in front-end','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="frontBootstrap" type="checkbox" id="resize" <?php echo( $frontBootstrap );?>> <?php _e('Automatically optimize images added by users in front end.','shortpixel-image-optimiser');?>
                        <p class="settings-info">
                            <?php _e('Check this if you have users that add images or PDF documents from custom forms in the front-end. This could increase the load on your server if you have a lot of users simultaneously connected.','shortpixel-image-optimiser');?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="autoMediaLibrary"><?php _e('Optimize media on upload','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <input name="autoMediaLibrary" type="checkbox" id="autoMediaLibrary" <?php echo( $autoMediaLibrary );?>> <?php _e('Automatically optimize Media Library items after they are uploaded (recommended).','shortpixel-image-optimiser');?>
                        <p class="settings-info">
                            <?php _e('By default, ShortPixel will automatically optimize all the freshly uploaded image and PDF files. If you uncheck this you\'ll need to either run Bulk ShortPixel or go to Media Library (in list view) and click on the right side "Optimize now" button(s).','shortpixel-image-optimiser');?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="saveAdv" id="saveAdv" class="button button-primary" title="<?php _e('Save Changes','shortpixel-image-optimiser');?>" value="<?php _e('Save Changes','shortpixel-image-optimiser');?>"> &nbsp;
            <input type="submit" name="saveAdv" id="bulkAdvGo" class="button button-primary" title="<?php _e('Save and go to the Bulk Processing page','shortpixel-image-optimiser');?>" value="<?php _e('Save and Go to Bulk Process','shortpixel-image-optimiser');?>"> &nbsp;
        </p>
        </div>
        <script>
            jQuery(document).ready(function () { ShortPixel.setupAdvancedTab();});
        </script>
        <?php }
    }
    
    function displaySettingsStats($quotaData, $averageCompression, $savedSpace, $savedBandwidth, 
                         $remainingImages, $totalCallsMade, $fileCount, $backupFolderSize) { ?>
        <a id="facts"></a>
        <h3><?php _e('Your ShortPixel Stats','shortpixel-image-optimiser');?></h3>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="averagCompression"><?php _e('Average compression of your files:','shortpixel-image-optimiser');?></label></th>
                    <td><?php echo($averageCompression);?>%</td>
                </tr>
                <tr>
                    <th scope="row"><label for="savedSpace"><?php _e('Saved disk space by ShortPixel','shortpixel-image-optimiser');?></label></th>
                    <td><?php echo($savedSpace);?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="savedBandwidth"><?php _e('Bandwith* saved with ShortPixel:','shortpixel-image-optimiser');?></label></th>
                    <td><?php echo($savedBandwidth);?></td>
                </tr>
            </tbody>
        </table>

        <p style="padding-top: 0px; color: #818181;" ><?php _e('* Saved bandwidth is calculated at 10,000 impressions/image','shortpixel-image-optimiser');?></p>

        <h3><?php _e('Your ShortPixel Plan','shortpixel-image-optimiser');?></h3>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row" bgcolor="#ffffff"><label for="apiQuota"><?php _e('Your monthly plan','shortpixel-image-optimiser');?>:</label></th>
                    <td bgcolor="#ffffff">
                        <?php 
                            $DateNow = time();
                            $DateSubscription = strtotime($quotaData['APILastRenewalDate']);
                            $DaysToReset = 30 - ((($DateNow  - $DateSubscription) / 84600) % 30);
                            printf(__('%s/month, renews in %s  days, on %s ( <a href="https://shortpixel.com/login/%s" target="_blank">Need More? See the options available</a> )','shortpixel-image-optimiser'),
                                $quotaData['APICallsQuota'], $DaysToReset,
                                date('M d, Y', strtotime(date('M d, Y') . ' + ' . $DaysToReset . ' days')), (defined("SHORTPIXEL_HIDE_API_KEY") ? '' : $this->ctrl->getApiKey()));?><br/>
                        <?php printf(__('<a href="https://shortpixel.com/login/%s/tell-a-friend" target="_blank">Join our friend referral system</a> to win more credits. For each user that joins, you receive +100 images credits/month.','shortpixel-image-optimiser'),
                                (defined("SHORTPIXEL_HIDE_API_KEY") ? '' : $this->ctrl->getApiKey()));?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="usedQUota"><?php _e('One time credits:','shortpixel-image-optimiser');?></label></th>
                    <td><?php echo(  number_format($quotaData['APICallsQuotaOneTimeNumeric']));?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="usedQUota"><?php _e('Number of images processed this month:','shortpixel-image-optimiser');?></label></th>
                    <td><?php echo($totalCallsMade);?> (<a href="https://api.shortpixel.com/v2/report.php?key=<?php echo(defined("SHORTPIXEL_HIDE_API_KEY") ? '' : $this->ctrl->getApiKey());?>" target="_blank">
                            <?php _e('see report','shortpixel-image-optimiser');?>
                        </a>)
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="remainingImages"><?php _e('Remaining** images in your plan:','shortpixel-image-optimiser');?></label></th>
                    <td><?php echo($remainingImages);?> <?php _e('images','shortpixel-image-optimiser');?></td>
                </tr>
            </tbody>
        </table>

        <p style="padding-top: 0px; color: #818181;" >
            <?php printf(__('** Increase your image quota by <a href="https://shortpixel.com/login/%s" target="_blank">upgrading your ShortPixel plan.</a>','shortpixel-image-optimiser'),
                    defined("SHORTPIXEL_HIDE_API_KEY") ? '' : $this->ctrl->getApiKey());?>
        </p>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="totalFiles"><?php _e('Total number of processed files:','shortpixel-image-optimiser');?></label></th>
                    <td><?php echo($fileCount);?></td>
                </tr>
                <?php if(true || $this->ctrl->backupImages()) { ?>
                <tr>
                    <th scope="row"><label for="sizeBackup"><?php _e('Original images are stored in a backup folder. Your backup folder size is now:','shortpixel-image-optimiser');?></label></th>
                    <td>
                        <form action="" method="POST">
                            <?php if ($backupFolderSize === null) { ?> 
                                <span id='backup-folder-size'>Calculating...</span>
                            <?php } else { echo($backupFolderSize); }?>
                            <input type="submit"  style="margin-left: 15px; vertical-align: middle;" class="button button-secondary" name="emptyBackup" value="<?php _e('Empty backups','shortpixel-image-optimiser');?>"/>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table> 
        <div style="display:none">

        </div>    
        <?php        
    }

    public function renderCustomColumn($id, $data, $extended = false){ ?> 
        <div id='sp-msg-<?php echo($id);?>' class='column-wp-shortPixel'>
            
            <?php switch($data['status']) {
                case 'n/a': ?> 
                    <?php _e('Optimization N/A','shortpixel-image-optimiser');?> <?php
                    break;
                case 'notFound': ?> 
                    <?php _e('Image does not exist.','shortpixel-image-optimiser');?> <?php
                    break;
                case 'invalidKey': 
                    if(defined("SHORTPIXEL_API_KEY")) { // multisite key - need to be validated on each site but it's not invalid
                        ?> <?php _e('Please <a href="options-general.php?page=wp-shortpixel">go to Settings</a> to validate the API Key.','shortpixel-image-optimiser');?> <?php
                    } else {
                        ?> <?php _e('Invalid API Key. <a href="options-general.php?page=wp-shortpixel">Check your Settings</a>','shortpixel-image-optimiser');?> <?php
                    } 
                    break;
                case 'quotaExceeded': 
                    echo($this->getQuotaExceededHTML(isset($data['message']) ? $data['message'] : ''));
                    break;
                case 'optimizeNow': 
                    if($data['showActions']) { ?>  
                        <a class='button button-smaller button-primary' href="javascript:manualOptimization('<?php echo($id)?>', false)">
                            <?php _e('Optimize now','shortpixel-image-optimiser');?>
                        </a> 
                    <?php }
                    echo($data['message']);
                    if(isset($data['thumbsTotal']) && $data['thumbsTotal'] > 0) {
                        echo("<br>+" . $data['thumbsTotal'] . " thumbnails");
                    }
                    break;
                case 'retry':
                        echo($data['message']);
                        if(isset($data['cleanup'])) {?>  <a class='button button-smaller button-primary' href="javascript:manualOptimization('<?php echo($id)?>', true)">
                            <?php _e('Cleanup&Retry','shortpixel-image-optimiser');?>
                            </a> <?php 
                        } else {
                            ?> 
                            <a class='button button-smaller button-primary' href="javascript:manualOptimization('<?php echo($id)?>', false)">
                                <?php _e('Retry','shortpixel-image-optimiser');?>
                            </a> <?php
                        }
                    break;
                case 'pdfOptimized': 
                case 'imgOptimized': 
                    $successText = $this->getSuccessText($data['percent'],$data['bonus'],$data['type'],$data['thumbsOpt'],$data['thumbsTotal'], $data['retinasOpt']);
                    if($extended) {
                        $missingThumbs = '';
                        if(count($data['thumbsMissing'])) {
                            $missingThumbs .= "<br><span style='font-weight: bold;'>" . __("Missing thumbs:", 'shortpixel-image-optimiser');
                            foreach($data['thumbsMissing'] as $miss) {
                                $missingThumbs .= "<br> &#8226; " . $miss;
                            }
                            $missingThumbs .= '</span>';
                        }
                        $successText .= ($data['webpCount'] ? "<br>+" . $data['webpCount'] . __(" WebP images", 'shortpixel-image-optimiser') : "")
                                . "<br>EXIF: " . ($data['exifKept'] ? __('kept','shortpixel-image-optimiser') :  __('removed','shortpixel-image-optimiser')) 
                                . "<br>" . __("Optimized on", 'shortpixel-image-optimiser') . ": " . $data['date']
                                . $missingThumbs; 
                    }
                    $this->renderListCell($id, $data['status'], $data['showActions'], 
                            (!$data['thumbsOpt'] && $data['thumbsTotal']) //no thumb was optimized
                            || (count($data['thumbsOptList']) && ($data['thumbsTotal'] - $data['thumbsOpt'] > 0)), $data['thumbsTotal'] - $data['thumbsOpt'], 
                            $data['backup'], $data['type'], $data['invType'], $successText);
                    
                    break;
                }
                //die(var_dump($data));
                ?>
        </div>
        <?php 
    }
    
    public function getSuccessText($percent, $bonus, $type, $thumbsOpt = 0, $thumbsTotal = 0, $retinasOpt = 0) {
        return   ($percent ? __('Reduced by','shortpixel-image-optimiser') . ' <strong>' . $percent . '%</strong> ' : '')
                .(!$bonus ? ' ('.$type.')':'')
                .($bonus && $percent ? '<br>' : '') 
                .($bonus ? __('Bonus processing','shortpixel-image-optimiser') : '') 
                .($bonus ? ' ('.$type.')':'') . '<br>'
                .($thumbsOpt ? ( $thumbsTotal > $thumbsOpt 
                        ? sprintf(__('+%s of %s thumbnails optimized','shortpixel-image-optimiser'),$thumbsOpt,$thumbsTotal) 
                        : sprintf(__('+%s thumbnails optimized','shortpixel-image-optimiser'),$thumbsOpt)) : '')
                .($retinasOpt ? '<br>' . sprintf(__('+%s Retina images optimized','shortpixel-image-optimiser') , $retinasOpt) : '' ) ;
    }
    
    public function renderListCell($id, $status, $showActions, $optimizeThumbs, $thumbsRemain, $backup, $type, $invType, $message, $extraClass = '') {
        if($showActions) { ?>
            <div class='sp-column-actions <?php echo($extraClass);?>'>
                <div class="sp-dropdown">
                    <button onclick="ShortPixel.openImageMenu(event);" class="sp-dropbtn button <?php if($optimizeThumbs) { echo('button-primary'); } ?> dashicons dashicons-menu" title="ShortPixel Actions"></button>
                    <div id="sp-dd-<?php echo($id);?>" class="sp-dropdown-content">
                        <?php if($status == 'imgOptimized') { ?>
                            <a class="sp-action-compare" href="javascript:ShortPixel.loadComparer('<?php echo($id);?>')" title="Compare optimized image with the original">Compare</a>
                        <?php } ?>
                        <?php if($optimizeThumbs) { ?>
                        <a class="sp-action-optimize-thumbs" href="javascript:optimizeThumbs(<?php echo($id)?>);" style="background-color:#0085ba;color:white;">
                            <?php printf(__('Optimize %s  thumbnails','shortpixel-image-optimiser'),$thumbsRemain);?>
                        </a>
                        <?php }
                        if($backup) {
                            if($type) { 
                                //$invType = $type == 'lossy' ? 'lossless' : 'lossy'; ?>
                                <a class="sp-action-reoptimize1" href="javascript:reoptimize('<?php echo($id)?>', '<?php echo($invType[0])?>');" 
                                   title="<?php _e('Reoptimize from the backed-up image','shortpixel-image-optimiser');?>">
                                    <?php _e('Re-optimize','shortpixel-image-optimiser');?> <?php echo($invType[0])?>
                                </a>
                                <a class="sp-action-reoptimize2" href="javascript:reoptimize('<?php echo($id)?>', '<?php echo($invType[1])?>');" 
                                   title="<?php _e('Reoptimize from the backed-up image','shortpixel-image-optimiser');?>">
                                    <?php _e('Re-optimize','shortpixel-image-optimiser');?> <?php echo($invType[1])?>
                                </a><?php
                            } ?>
                            <a class="sp-action-restore" href="admin.php?action=shortpixel_restore_backup&attachment_ID=<?php echo($id)?>">
                                <?php _e('Restore backup','shortpixel-image-optimiser');?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div> 
        <?php } ?> 
        <div class='sp-column-info'>
            <?php echo($message);?>
        </div> <?php
    }
    
    public function getQuotaExceededHTML($message = '') {
        return "<div class='sp-column-actions' style='width:110px;'> 
        <a class='button button-smaller button-primary' href='https://shortpixel.com/login/". (defined("SHORTPIXEL_HIDE_API_KEY") ? '' : $this->ctrl->getApiKey()) . "' target='_blank'>"
            . __('Extend Quota','shortpixel-image-optimiser') . 
        "</a>
        <a class='button button-smaller' href='admin.php?action=shortpixel_check_quota'>" 
            . __('Check&nbsp;&nbsp;Quota','shortpixel-image-optimiser') .
        "</a></div>
        <div class='sp-column-info'>" . $message . " Quota Exceeded.</div>";
    }
    
    public function outputComparerHTML() {?>
        <div class="sp-modal-shade">
            <div id="spUploadCompare" class="shortpixel-modal shortpixel-hide">
              <div class="sp-modal-title">
                <button type="button" class="sp-close-button">&times;</button>
                <?php _('Compare Images', 'shortpixel-image-optimiser');?>
              </div>
              <div class="sp-modal-body sptw-modal-spinner" style="height:400px;padding:0;">
                <div class="shortpixel-slider" style="z-index:2000;">
                    <div class="twentytwenty-container" id="spCompareSlider">
                        <img class="spUploadCompareOriginal"/>
                        <img class="spUploadCompareOptimized"/>
                    </div>
                </div>
              </div>
            </div>
            <div id="spUploadCompareSideBySide" class="shortpixel-modal shortpixel-hide">
              <div class="sp-modal-title">
                <button type="button" class="sp-close-button">&times;</button>
                Compare Images
              </div>
              <div class="sp-modal-body" style="height:400px;padding:0;">
                <div class="shortpixel-slider"style="text-align: center;">
                    <div class="side-by-side" style="text-align: center; display:inline-block;">
                        <img class="spUploadCompareOriginal" style="margin: 10px"/><br>
                        Original
                    </div>
                    <div class="side-by-side" style="text-align: center; display:inline-block;">
                        <img class="spUploadCompareOptimized" style="margin: 10px"/><br>
                        Optimized
                    </div>
                </div>
              </div>
            </div>
        </div>
        <?php
    }
}
