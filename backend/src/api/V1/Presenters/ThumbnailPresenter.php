<?php

namespace Api\V1\Presenters;

use Tooleks\Laravel\Presenter\Presenter;

/**
 * Class TokenPresenter.
 *
 * @property string absolute_url
 * @property int width
 * @property int height
 * @package Api\V1\Presenters
 */
class ThumbnailPresenter extends Presenter
{
    /**
     * @inheritdoc
     */
    protected function getAttributesMap() : array
    {
        return [
            'url' => function () {
                $relativeUrl = $this->getPresenteeAttribute('relative_url');
                return $relativeUrl ? url(config('main.storage.url')) . $relativeUrl : null;
            },
            'width' => 'width',
            'height' => 'height',
        ];
    }
}
