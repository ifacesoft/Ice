<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 19.04.16
 * Time: 16:08
 */

namespace Ice\WidgetComponent;

use Ice\Core\Render;
use Ice\Core\Resource;
use Ice\Helper\String;
use Ice\Render\Replace;

class Form_ListBox extends FormElement_TextInput
{
    public function getTitle($item = null)
    {
        $itemTitle = $this->getItemTitle();

        if ($item === null) {
            return $itemTitle;
        }

        $resourceClass = $this->getOption('itemTitleResource', null);

        if ($resourceClass === null) {
            $resourceClass = $this->getOption('itemTitleHardResource', null);
        }

        $template = null;

        if ($resourceClass) {
            $template = $itemTitle;

            if ($this->getOption('itemTitleHardResource', null)) {
                $template .= '_' . $item[$itemTitle];
            }
        }

        /** @var Resource $resource */
        $resource = $resourceClass === true
            ? $this->getResource()
            : ($resourceClass === null ? $resourceClass : Resource::create($resourceClass));

        if ($template) {
            $title = $resource
                ? $resource->get($template, $item)
                : Replace::getInstance()->fetch($template, $item, null, Render::TEMPLATE_TYPE_STRING);
        } else {
            $title = $item[$itemTitle];
        }

        if ($truncate = $this->getOption('itemTitleTruncate')) {
            $title = String::truncate($title, $truncate);
        }

        return htmlentities($title);
    }

    public function getItemTitle()
    {
        return $this->getOption('itemTitle', 'itemTitle');
    }

    /**
     * @return null
     */
    public function getItems()
    {
        $items = $this->getOption('items', []);

        if ($this->getOption('required', false) === false) {
            return array_merge([[$this->getItemKey() => null, $this->getItemTitle() => $this->getItemEmpty()]], $items);
        }

        return $items;
    }

    public function getItemKey()
    {
        return $this->getOption('itemKey', 'itemKey');
    }

    public function getItemEmpty()
    {
        return $this->getOption('itemEmpty', '');
    }
}