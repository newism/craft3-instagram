<?php
/**
 * Instagram plugin for Craft CMS 3.x
 *
 * Instagram plugin to connect users / site to the Instagram Basic API
 *
 * @link      https://newism.com.au
 * @copyright Copyright (c) 2020 Leevi Graham
 */

namespace newism\instagram\assetbundles\instagram;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Leevi Graham
 * @package   Instagram
 * @since     1.0.0
 */
class InstagramAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@newism/instagram/assetbundles/instagram/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Instagram.js',
        ];

        $this->css = [
            'css/Instagram.css',
        ];

        parent::init();
    }
}
