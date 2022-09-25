<?php

declare(strict_types=1);

namespace App\Controller\Traits;

trait DataTrait
{
    public function getArticleJsonArrData($items)
    {
        if ($items) {
            foreach ($items as $item) {
                if ($item->getId()) {
                    $itemId = $item->getId();
                }
                if ($item->getName()) {
                    $itemTitle = $item->getName();
                } else {
                    $itemTitle = null;
                }
                if ($item->getBodytext()) {
                    $itemDescription = $item->getBodytext();
                } else {
                    $itemDescription = null;
                }

                $arrData[] = [
                    'id' => $itemId,
                    'title' => $itemTitle,
                    'description' => $itemDescription
                ];
            }
            return $arrData;
        } else {
            return null;
        }
    }

    public function getJsonArrData($items)
    {
        if ($items) {
            foreach ($items as $item) {
                if ($item->getId()) {
                    $itemId = $item->getId();
                }
                if ($item->getName()) {
                    $itemTitle = $item->getName();
                } else {
                    $itemTitle = null;
                }
                if ($item->getDescription()) {
                    $itemDescription = $item->getDescription();
                } else {
                    $itemDescription = null;
                }
                if ($item->getImage()) {
                    $itemImage = $item->getImage();
                } else {
                    $itemImage = null;
                }

                $arrData[] = [
                    'id' => $itemId,
                    'title' => $itemTitle,
                    'description' => $itemDescription,
                    'image' => $itemImage
                ];
            }
            return $arrData;
        } else {
            return null;
        }
    }
}
