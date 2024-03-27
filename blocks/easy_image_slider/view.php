<?php

use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Page\Page;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Block\View\BlockView|Concrete\Core\Page\Type\Composer\Control\BlockControl $this
 * @var EasyImageSlider\Options $options
 * @var int|string $bID
 * @var Concrete\Core\File\File[] $files
 */

$c = Page::getCurrentPage();
$galleryHasImage = false;
$dtatitle = NULL;
if ($c->isEditMode()) {
    ?>
    <div class="ccm-edit-mode-disabled-item">
        <div style="padding: 40px 0px 40px 0px"><?php echo t('Easy Gallery disabled in edit mode.') ?></div>
    </div>
    <?php
} elseif (!empty($files)) {
    $type = Type::getByHandle('file_manager_detail');
    $firstFile = $files[0];
    $firstWrapperBg = $options->isTransparent ? ($firstFile->getAttribute('image_bg_color') ? $firstFile->getAttribute('image_bg_color') : $options->fadingColor) : $options->fadingColor;
    ?>
    <div class="easy-slider easy-slider-one <?php echo $options->isSingleItemSlide() ? 'easy-slider-single' : '' ?>" id="easy-slider-wrapper-<?php echo $bID ?>" style="background-color:<?php echo $firstWrapperBg ?>" data-colorbg="<?php echo $options->fadingColor ?>">
        <div class="easy-slider-carousel-inner <?php echo $options->responsiveContainer ? 'responsive-container' : '' ?>" id="easy-slider-<?php echo $bID ?>">
            <?php
            foreach ($files as $key => $f) {
                $galleryHasImage = true;
                // Different Thumbnails sizes
                $thumbnailUrl = $options->isSingleItemSlide() ? $f->getRelativePath() : $f->getThumbnailURL($type->getBaseVersion());
                // Due to a bug in OWL2 Lazy work only with loop activated.
                $placeHolderUrl = $options->lazy ? ($this->getBlockURL() . "/images/placeholders/placeholder-{$f->getAttribute('width')}-{$f->getAttribute('height')}.png") : $thumbnailUrl;
                $retinaThumbnailUrl = $options->isSingleItemSlide() ? $f->getRelativePath() : $f->getThumbnailURL($type->getDoubledVersion());
                // Styles for color on hover
                $thumbnailBackground = $options->isTransparent ? 'background-color:transparent' : ('background-color:' . ($f->getAttribute('image_bg_color') ? $f->getAttribute('image_bg_color') : $options->fadingColor) . ';');
                
				// Full image infos
                $fullUrl = $f->getRelativePath();
                // Images Links, title and description
                $linkUrl = $f->getAttribute('image_link');
                $linkUrlText = $f->getAttribute('image_link_text');
                $displayInfos = $options->ItemsTitle || $options->ItemsDescription || $linkUrl && $linkUrlText;
                ?>
                <div class="item" id="item-<?php echo $key ?>" style="<?php echo $thumbnailBackground ?>" <?php if ($f->getAttribute('image_bg_color')) { ?>data-color="<?php echo $f->getAttribute('image_bg_color') ?><?php } ?>">
                    <?php
                    if ($options->lightbox !== '' && !$linkUrl) {
						
						if ($options->lightboxTitle) { $dtatitle = preg_replace('/\.\w+$/', '', $f->getTitle()); }
						
                        echo '<a href="', h($fullUrl), '" data-image="', h($fullUrl), '" "data-title="', h($dtatitle), '" data-lightbox="gallery', $bID, '"';
                        if ($options->lightboxTitle) {
                            echo ' title="', h($dtatitle); //h('<b>' . h($dtatitle) . '</b>');
                            if ($options->lightboxDescription) {
                                echo h('<br />' . $f->getDescription());
                            }
                            echo '"';
                        }
                        echo '>';
                    }
                    ?>
                    <img src="<?php echo $placeHolderUrl ?>" data-src="<?php echo $retinaThumbnailUrl ?>" alt="<?php echo h($dtatitle) ?>" <?php if ($options->lazy) { ?> class="lazyOwl" <?php } ?> />
                    <?php
                    if ($displayInfos) {
                        ?>
                        <div class="info-wrap">
                            <div class="info">
                                <div style="<?php echo $thumbnailInfoBackground ?>">
                                    <?php
                                    if($options->ItemsTitle) {
                                        ?>
                                        <p class="title"><?php echo $f->getTitle() ?></p>
                                        <?php
                                    }
                                    if ($options->ItemsDescription) {
                                        ?>
                                        <p class="description"><small><?php echo $f->getDescription() ?></small></p>
                                        <?php
                                    }
                                    if ($linkUrl && $linkUrlText) {
                                        ?>
                                        <p class="link"><a href="<?php echo $linkUrl ?>"><?php echo $linkUrlText ?></a></p>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    if ($options->lightbox !== '' && !$linkUrl) {
                        echo '</a>';
                    }
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
    
    <div class="owl-nav owl-controls" id="owl-navigation-<?php echo $bID ?>"></div>
    
    </div>
    
    <style>
	
	#owl-navigation-<?php echo $bID ?> .owl-buttons .owl-prev,
	#owl-navigation-<?php echo $bID ?> .owl-buttons .owl-next { 
		zoom: 1;
		background: rgba(0,0,0,0.9);
		border-radius: 30px;
		color: #fff;
		display: inline-block;
		filter: Alpha(Opacity=50);
		font-size: 12px;
		margin: 0 10px;
		opacity: .95;
		padding: 30px 15px;
		position: absolute;
		z-index: 10;
		top: 45%;
		border-radius: 0;
		width: auto;
		height: auto;
		font-size: 1.25rem;
	}
	#owl-navigation-<?php echo $bID ?> .owl-prev {left: 0; }
	#owl-navigation-<?php echo $bID ?> .owl-next {right: 0; }
	
	</style>

    <?php
    if ($galleryHasImage) {
        $this->inc('elements/javascript.php');
        $this->inc('elements/css.php');
    }
}
