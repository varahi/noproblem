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
                if ($item->getImage()) {
                    $itemImage = $item->getImage();
                } else {
                    $itemImage = null;
                }
                if ($item->getTeaser()) {
                    $itemTeaser = $item->getTeaser();
                } else {
                    $itemTeaser = null;
                }
                if ($item->getBodytext()) {
                    $itemDescription = $item->getBodytext();
                } else {
                    $itemDescription = null;
                }
                if ($item->getSlug()) {
                    $itemSlug = $item->getSlug();
                } else {
                    $itemSlug = null;
                }

                $arrData[] = [
                    'id' => $itemId,
                    'title' => $itemTitle,
                    'description' => $itemDescription,
                    'teaser' => $itemTeaser,
                    'image' => $itemImage,
                    'slug' => $itemSlug
                ];
            }
            return $arrData;
        } else {
            return null;
        }
    }

    public function getCourseJsonArrData($items)
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
                if ($item->getImage()) {
                    $itemImage = $item->getImage();
                } else {
                    $itemImage = null;
                }
                if ($item->getTeaser()) {
                    $itemTeaser = $item->getTeaser();
                } else {
                    $itemTeaser = null;
                }
                if ($item->getBodytext()) {
                    $itemDescription = $item->getBodytext();
                } else {
                    $itemDescription = null;
                }
                if ($item->getSlug()) {
                    $itemSlug = $item->getSlug();
                } else {
                    $itemSlug = null;
                }
                if ($item->getPrice()) {
                    $itemPrice = $item->getPrice();
                } else {
                    $itemPrice = null;
                }

                $arrData[] = [
                    'id' => $itemId,
                    'title' => $itemTitle,
                    'description' => $itemDescription,
                    'teaser' => $itemTeaser,
                    'image' => $itemImage,
                    'slug' => $itemSlug,
                    'price' => $itemPrice
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
                if ($item->getSlug()) {
                    $itemSlug = $item->getSlug();
                } else {
                    $itemSlug = null;
                }

                $arrData[] = [
                    'id' => $itemId,
                    'title' => $itemTitle,
                    'description' => $itemDescription,
                    'image' => $itemImage,
                    'slug' => $itemSlug
                ];
            }
            return $arrData;
        } else {
            return null;
        }
    }
}
