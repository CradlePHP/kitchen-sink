<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\App\Core;

/**
 * Methods given the app that will return 3rd party services
 *
 * @vendor   Custom
 * @package  Component
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class Seo
{
    /**
     * Returns an SEO friendly product phrase
     *
     * @param array $data
     * @param int   $total
     * @param array $keys
     *
     * @return string
     */
    public static function getProductPhrase(array $data = [], $total = 0, array $keys = [])
    {
        foreach ($keys as $key) {
            if ($key === 'date') {
                $data['date'] = date('M Y');
                continue;
            }

            if ($key === 'total') {
                $data['total'] = $total;
                continue;
            }

            if ($key === 'site') {
                $data['site'] = cradle('global')->l10n('name');
                continue;
            }

            if ($key === 'location') {
                $data['location'] = cradle('global')->l10n('location');
                continue;
            }

            if (!isset($data[$key]) || empty($data[$key])) {
                $data[$key] = [];
                continue;
            }

            if ($key === 'price_range') {
                $data['currency'] = cradle('global')->currency();
                $data['price_range'] = explode(',', $data['price_range']);
                continue;
            }

            if (is_scalar($data[$key])) {
                $data[$key] = [$data[$key]];
                continue;
            }
        }

        if (empty($data['product_brand'])) {
            $data['product_brand'] = ['All'];
        }

        if (empty($data['q'])) {
            $data['q'] = ['Products'];
        }

        //{CONDITION} {BRAND} {Q} in {TAG} between {CURRENCY} {price_range} and {price_range} by {profile_type}
        //{BRAND} {Q} {CONDITION} in {TAG} from {locale} between {CURRENCY} {price_range} and {price_range}
        //{BRAND} {Q} ? As of {MONTH} {YEAR} you can buy {TOTAL} in {locale} between {CURRENCY} {price_range} and {price_range}
        $phrase = [];
        $binds = [];
        foreach ($keys as $key) {
            if (empty($data[$key]) || !$data[$key]) {
                continue;
            }

            switch ($key) {
                case 'product_condition':
                case 'q':
                case 'product_brand':
                    $phrase[] = '%s';
                    $binds[] = ucwords($data[$key][0]);
                    break;
                case 'product_tags':
                    $phrase[] = 'in %s';
                    $binds[] = ucwords($data[$key][0]);
                    break;
                case 'profile_type':
                    $phrase[] = 'by %s';
                    $binds[] = ucwords($data[$key][0]) . 's';
                    break;
                case 'price_range':
                    $phrase[] = 'between %s%s - %s';
                    $binds[] = $data['currency'];
                    $binds[] = number_format($data[$key][0]);
                    $binds[] = number_format($data[$key][1]);
                    break;
                case 'location':
                    $phrase[] = 'from %s';
                    if (isset($data['product_location']) && is_array($data['product_location'])) {
                        $binds[] = ucwords($data['product_location'][0]);
                    } else if (isset($data['product_location'])) {
                        $binds[] = ucwords($data['product_location']);
                    } else {
                        $binds[] = ucwords($data[$key]);
                    }
                    break;
                case 'product_location':
                    if (isset($data['product_location'][0])) {
                        $phrase[] = 'from %s';
                        $binds[] = ucwords($data['product_location'][0]);
                    }
                    break;
                case 'date':
                    if ($data['total']) {
                        $phrase[] = '? As of %s';
                        $binds[] = $data[$key];
                    }
                    break;
                case 'total':
                    $phrase[] = 'you can buy %s';
                    $binds[] = $data[$key];
                    break;
                case 'site':
                    $phrase[] = 'found on %s';
                    $binds[] = ucwords($data[$key]);
                    break;
            }
        }

        $arguments = array_merge([implode(' ', $phrase)], $binds);
        return cradle('global')->translate(...$arguments);
    }

    /**
     * Returns an SEO friendly product keywords
     *
     * @param array $data
     * @param int   $limit
     *
     * @return string
     */
    public static function getProductKeywords(array $data = [], $limit = 5)
    {
        $suggestions = [];
        if (isset($data['q']) && is_string($data['q'])) {
            $suggestions[] = $data['q'];
        } else if (isset($data['q']) && is_array($data['q'])) {
            $suggestions = array_merge($suggestions, $data['q']);
        }

        if (isset($data['product_brand']) && is_string($data['product_brand'])) {
            $suggestions[] = $data['product_brand'];
        } else if (isset($data['product_brand']) && is_array($data['product_brand'])) {
            $suggestions = array_merge($suggestions, $data['product_brand']);
        }

        if (isset($data['product_tags']) && is_string($data['product_tags'])) {
            $suggestions[] = $data['product_tags'];
        } else if (isset($data['product_tags']) && is_array($data['product_tags'])) {
            $suggestions = array_merge($suggestions, $data['product_tags']);
        }

        if (isset($data['profile_type']) && is_string($data['profile_type'])) {
            $suggestions[] = $data['profile_type'];
        } else if (isset($data['profile_type']) && is_array($data['profile_type'])) {
            $suggestions = array_merge($suggestions, $data['profile_type']);
        }

        $suggestions[] = cradle('global')->l10n('locale');
        if (isset($data['product_condition']) && is_string($data['product_condition'])) {
            $suggestions[] = $data['product_condition'];
        } else if (isset($data['product_condition']) && is_array($data['product_condition'])) {
            $suggestions = array_merge($suggestions, $data['product_condition']);
        }

        $suggestions = array_merge($suggestions, [
            'products',
            'browse',
            'shop',
            'search',
            'compare',
            'buy'
        ]);

        $keywords = [];
        for ($i = 0; $i < $limit; $i++) {
            $keywords[] = $suggestions[$i];
        }

        return implode(',', $keywords);
    }

    /**
     * Returns an SEO friendly product URL
     *
     * @param array $data
     *
     * @return string
     */
    public static function getProductCanonicalUrl(array $data = [])
    {
        // Checks if there is a request uri and is a canonical url already
        if (strpos($_SERVER['REQUEST_URI'], '-can.') !== false) {
            return $_SERVER['REQUEST_URI'];
        }

        //Loops through the data
        foreach($data as $key => $d) {
            //Checks if this is an array
            if(is_array($d)) {
                //Loops through this array
                foreach($d as $dKey => $dd) {
                    $d[$dKey] = ucwords($dd);
                }
                //Returns back to the parent
                $data[$key] = $d;
            } else {
                //This is not an array
                $data[$key] = ucwords($d);
            }
        }

        //do the easy ones
        if (count($data) === 1) {
            switch (true) {
                case isset($data['product_brand']) && is_array($data['product_brand']):
                    return '/' . urlencode(implode('-', $data['product_brand'])) . '/brand';
                case isset($data['product_brand']):
                    return '/' . urlencode($data['product_brand']) . '/brand';
                case isset($data['product_tags']) && is_array($data['product_tags']):
                    return '/' . urlencode(implode('-', $data['product_tags'])) . '/tag';
                case isset($data['product_tags']):
                    return '/' . urlencode($data['product_tags']) . '/tag';
                case isset($data['q']) && is_array($data['q']):
                    return '/' . urlencode(implode('-', $data['q']));
                case isset($data['q']):
                    return '/' . urlencode($data['q']);
                case isset($data['product_condition']):
                    return '/' . $data['product_condition'] . '-can.c';
                case isset($data['price_range']):
                    return '/' . urlencode($data['price_range']) . '-can.p';
                case isset($data['profile_type']):
                    return '/' . $data['profile_type'] . '-can.u';
            }
        }

        //do the next easy ones
        if (count($data) === 2 && isset($data['start'])) {
            switch (true) {
                case isset($data['product_brand']) && is_array($data['product_brand']):
                    return '/' . urlencode(implode('-', $data['product_brand'])) . '/brand?start' . $data['start'];
                case isset($data['product_brand']):
                    return '/' . urlencode($data['product_brand']) . '/brand?start' . $data['start'];
                case isset($data['product_tags']) && is_array($data['product_tags']):
                    return '/' . urlencode(implode('-', $data['product_tags'])) . '/tag?start' . $data['start'];
                case isset($data['product_tags']):
                    return '/' . urlencode($data['product_tags']) . '/tag?start' . $data['start'];
                case isset($data['q']) && is_array($data['q']):
                    return '/' . urlencode(implode('-', $data['q'])) . '?start' . $data['start'];
                case isset($data['q']):
                    return '/' . urlencode($data['q']) . '?start' . $data['start'];
                case isset($data['product_condition']):
                    return '/' . $data['product_condition'] . '-can.c?start' . $data['start'];
                case isset($data['price_range']):
                    return '/' . urlencode($data['price_range']) . '-can.p?start' . $data['start'];
                case isset($data['profile_type']):
                    return '/' . $data['profile_type'] . '-can.u?start' . $data['start'];
            }
        }

        //do the canonical shuffle
        $keys = [];
        $values = [];
        foreach ($data as $key => $value) {
            switch (true) {
                case $key === 'product_brand' && is_array($value):
                    $keys[] = 'b'.count($value);
                    $values = array_merge($values, $value);
                    break;
                case $key === 'product_brand':
                    $keys[] = 'b';
                    $values[] = $value;
                    break;
                case $key === 'product_tags' && is_array($value):
                    $keys[] = 't'.count($value);
                    $values = array_merge($values, $value);
                    break;
                case $key === 'product_tags':
                    $keys[] = 't';
                    $values[] = $value;
                    break;
                case $key === 'q' && is_array($value):
                    $keys[] = 'q'.count($value);
                    $values = array_merge($values, $value);
                    break;
                case $key === 'q':
                    $keys[] = 'q';
                    $values[] = $value;
                    break;
                case $key === 'product_condition':
                    $keys[] = 'c';
                    $values[] = $value;
                    break;
                case $key === 'price_range':
                    $keys[] = 'p';
                    $values[] = $value;
                    break;
                case $key === 'profile_type':
                    $keys[] = 'u';
                    $values[] = $value;
                    break;
            }
        }

        foreach ($values as $i => $value) {
            $values[$i] = urlencode($value);
        }

        if (isset($data['start'])) {
            return  '/' . implode('-', $values) . '-can.' . implode('.', $keys) . '?start=' . $data['start'];
        }

        return '/' . implode('-', $values) . '-can.' . implode('.', $keys);
    }

    /**
     * Returns an SEO friendly product query
     *
     * @param array $data
     *
     * @return string
     */
    public static function getProductCanonicalQuery(array $data = [])
    {
        //do the canonical shuffle
        foreach ($data as $key => $value) {
            switch (true) {
                case $key === 'product_brand':
                case $key === 'product_tags':
                case $key === 'q':
                case $key === 'product_condition':
                case $key === 'price_range':
                case $key === 'profile_type':
                case $key === 'start':
                    unset($data[$key]);
                    break;
            }
        }

        if (empty($data)) {
            return '';
        }

        return '?' . http_build_query($data);
    }
}
