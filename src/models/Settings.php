<?php
/**
 * Instagram plugin for Craft CMS 3.x
 *
 * Instagram plugin to connect users / site to the Instagram Basic API
 *
 * @link      https://newism.com.au
 * @copyright Copyright (c) 2020 Leevi Graham
 */

namespace newism\instagram\models;

use newism\instagram\Plugin;

use Craft;
use craft\base\Model;

/**
 * @author    Leevi Graham
 * @package   Instagram
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $clientId = '';
    public $clientSecret = '';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['clientId', 'string'],
            ['clientId', 'default', 'value' => ''],
            ['clientSecret', 'string'],
            ['clientSecret', 'default', 'value' => ''],
        ];
    }
}
