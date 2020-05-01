<?php
/**
 * Instagram plugin for Craft CMS 3.x
 *
 * Instagram plugin to connect users / site to the Instagram Basic API
 *
 * @link      https://newism.com.au
 * @copyright Copyright (c) 2020 Leevi Graham
 */

namespace newism\instagram\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\db\Query;
use craft\db\Table;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use League\OAuth2\Client\Token\AccessToken;
use newism\instagram\assetbundles\instagramfield\InstagramFieldAsset;
use newism\instagram\records\AccessTokenRecord;
use yii\db\Schema;

/**
 * @author    Leevi Graham
 * @package   Instagram
 * @since     1.0.0
 */
class Instagram extends Field
{

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $token = [];

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('instagram', 'Instagram');
    }

    // Public Methods
    // =========================================================================

    public static function hasContentColumn(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge(
            $rules,
            [
                ['token', 'default', 'value' => null],
            ]
        );

        return $rules;
    }

    /**
     * @inheritdoc
     * @throws \JsonException
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if($value === null && $element !== null && $element->id) {
            $accessTokenRecord = AccessTokenRecord::findOne([
                'siteId' => $element->siteId,
                'elementId' => $element->id,
                'fieldId' => $this->id,
            ]);
            if($accessTokenRecord) {
                $value = $accessTokenRecord->token;
            }
        }

        if(!empty($value)) {
            $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            $value = new AccessToken($value);
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(InstagramFieldAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
            'connectUrl' => UrlHelper::cpUrl('instagram/connect', ['stateKey' => 'fieldId_'.$id]),
            'siteUri' => rtrim(UrlHelper::siteUrl(), '/'),
        ];

        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').InstagramInstagram(${jsonVars});");

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'instagram/_components/fields/Instagram_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
            ]
        );
    }

    public function afterElementSave(ElementInterface $element, bool $isNew)
    {
        parent::afterElementSave($element, $isNew);

        /** @var AccessToken $accessToken */
        $accessToken = $element->{$this->handle};
        $accessTokenRecord = null;

        // Try and find an access token record
        if($element && $element->id) {
            $accessTokenRecord = AccessTokenRecord::findOne(['elementId' => $element->id]);
            // If it exists and the value is empty
            // delete the old record
            if($accessTokenRecord && empty($accessToken)) {
                $accessTokenRecord->delete();
                $accessTokenRecord = null;
            }
        }

        // If there's no record and the value is empty
        // nothing to do so return
        if($accessTokenRecord === null && empty($accessToken)) {
            return;
        }

        // No record for this entry
        if($accessTokenRecord === null) {
            $accessTokenRecord = new AccessTokenRecord();
        }

        // There's a record and a value
        $accessTokenRecord->elementId = $element->id;
        $accessTokenRecord->siteId = $element->siteId;
        $accessTokenRecord->fieldId = $this->id;
        $accessTokenRecord->token = $accessToken;
        $accessTokenRecord->expires = $accessToken->getExpires();
        $accessTokenRecord->save();
    }
}
