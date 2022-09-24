<?php

/**
 * Helpers functions
 *
 * @author   WEBHD
 */

use EHD\Helpers\Cast;
use EHD\Helpers\Str;
use EHD\Helpers\Url;

\defined( '\WPINC' ) || die;

// -------------------------------------------------------------

if ( ! function_exists( 'json_encode_prettify' ) ) {
    /**
     * @param $data
     *
     * @return false|string
     */
    function json_encode_prettify( $data ) {
        return json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
    }
}

// ------------------------------------------------------

if ( ! function_exists( 'safe_mailto' ) ) {
    /**
     * Encoded Mailto Link
     *
     * Create a spam-protected mailto link written in Javascript
     *
     * @param string $email the email address
     * @param string $title the link title
     * @param string|null $attributes any attributes
     *
     * @return string
     */
    function safe_mailto($email, string $title = '', ?string $attributes = '' ) {
        if ( trim( $title ) === '' ) {
            $title = $email;
        }

        $x = str_split( '<a href="mailto:', 1 );

        for ( $i = 0, $l = strlen( $email ); $i < $l; $i ++ ) {
            $x[] = '|' . ord( $email[ $i ] );
        }

        $x[] = '"';

        if ( $attributes !== '' ) {
            if ( is_array( $attributes ) ) {
                foreach ( $attributes as $key => $val ) {
                    $x[] = ' ' . $key . '="';
                    for ( $i = 0, $l = strlen( $val ); $i < $l; $i ++ ) {
                        $x[] = '|' . ord( $val[ $i ] );
                    }
                    $x[] = '"';
                }
            } else {
                for ( $i = 0, $l = mb_strlen( $attributes ); $i < $l; $i ++ ) {
                    $x[] = mb_substr( $attributes, $i, 1 );
                }
            }
        }

        $x[] = '>';

        $temp = [];
        for ( $i = 0, $l = strlen( $title ); $i < $l; $i ++ ) {
            $ordinal = ord( $title[ $i ] );

            if ( $ordinal < 128 ) {
                $x[] = '|' . $ordinal;
            } else {
                if ( empty( $temp ) ) {
                    $count = ( $ordinal < 224 ) ? 2 : 3;
                }

                $temp[] = $ordinal;
                if ( count( $temp ) === $count ) // @phpstan-ignore-line
                {
                    $number = ( $count === 3 ) ? ( ( $temp[0] % 16 ) * 4096 ) + ( ( $temp[1] % 64 ) * 64 ) + ( $temp[2] % 64 ) : ( ( $temp[0] % 32 ) * 64 ) + ( $temp[1] % 64 );
                    $x[]    = '|' . $number;
                    $count  = 1;
                    $temp   = [];
                }
            }
        }

        $x[] = '<';
        $x[] = '/';
        $x[] = 'a';
        $x[] = '>';

        $x = array_reverse( $x );

        // improve obfuscation by eliminating newlines & whitespace
        $output = '<script type="text/javascript">'
            . 'var l=new Array();';

        foreach ( $x as $i => $value ) {
            $output .= 'l[' . $i . "] = '" . $value . "';";
        }

        return $output . ( 'for (var i = l.length-1; i >= 0; i=i-1) {'
                . "if (l[i].substring(0, 1) === '|') document.write(\"&#\"+unescape(l[i].substring(1))+\";\");"
                . 'else document.write(unescape(l[i]));'
                . '}'
                . '</script>' );
    }
}

// ------------------------------------------------------

if ( ! function_exists( 'is_php' ) ) {
    /**
     * @param string $version
     *
     * @return  bool
     */
    function is_php( string $version = '5.0.0' ) {

        static $phpVer;
        if ( ! isset( $phpVer[ $version ] ) ) {
            $phpVer[ $version ] = ! ( ( version_compare( PHP_VERSION, $version ) < 0 ) );
        }

        return $phpVer[ $version ];
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'is_empty' ) ) {
    /**
     * @param mixed $value
     *
     * @return bool
     */
    function is_empty( $value ) {
        if ( is_string( $value ) ) {
            return trim( $value ) === '';
        }

        return ! is_numeric( $value ) && ! is_bool( $value ) && empty( $value );
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'is_not_empty' ) ) {
    /**
     * @param mixed $value
     *
     * @return boolean
     */
    function is_not_empty( $value ) {
        return ! is_empty( $value );
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'millitime' ) ) {
    /**
     * @return string
     */
    function millitime() {
        $microtime = microtime();
        $comps = explode(' ', $microtime);

        // Note: Using a string here to prevent loss of precision
        // in case of "overflow" (PHP converts it to a double)
        return sprintf( '%d%03d', $comps[1], $comps[0] * 1000 );
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'ip_address' ) ) {
    /**
     * @return string
     */
    function ip_address() {
        $local = '127.0.0.1';

        // Get user IP address
        foreach (
            [
                'HTTP_CLIENT_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'REMOTE_ADDR',
            ] as $key
        ) {
            if ( array_key_exists( $key, $_SERVER ) === true ) {
                foreach ( explode( ',', $_SERVER[ $key ] ) as $ip ) {
                    $ip = trim( $ip ); // just to be safe
                    $ip = ( validate_ip( $ip ) === false ) ? $local : $ip;
                }
            }
        }

        return $ip;
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'validate_ip' ) ) {
    /**
     * @param $ip
     *
     * @return bool
     */
    function validate_ip( $ip ) {
        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
            return false;
        }

        return true;
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'youtube_iframe' ) ) {
    /**
     * @param $url
     * @param int $autoplay
     * @param bool $lazyload
     * @param bool $control
     *
     * @return string|null
     */
    function youtube_iframe( $url, int $autoplay = 0, bool $lazyload = true, bool $control = true ) {
        parse_str( parse_url( $url, PHP_URL_QUERY ), $vars );
        if ( isset( $vars['v'] ) ) {
            $idurl     = $vars['v'];
            $_size     = ' width="800px" height="450px"';
            $_autoplay = 'autoplay=' . $autoplay;
            $_auto     = ' allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"';
            if ( $autoplay == 1 ) {
                $_auto = ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"';
            }
            $_src     = 'https://www.youtube.com/embed/' . $idurl . '?wmode=transparent&origin=' . home_url() . '&' . $_autoplay;
            $_control = '';
            if ( $control == false ) {
                $_control = '&modestbranding=1&controls=0&rel=0&version=3&loop=1&enablejsapi=1&iv_load_policy=3&playlist=' . $idurl . '&playerapiid=ng_video_iframe_' . $idurl;
            }
            $_src  .= $_control . '&html5=1';
            $_src  = ' src="' . $_src . '"';
            $_lazy = '';
            if ( $lazyload == true ) {
                $_lazy = ' loading="lazy"';
            }
            $_iframe = '<iframe id="ytb_iframe_' . $idurl . '" title="YouTube Video Player" allowfullscreen' . $_lazy . $_auto . $_size . $_src . ' style="border:0"></iframe>';

            return $_iframe;
        }

        return null;
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'youtube_image' ) ) {
    /**
     * @param $url
     * @param array $resolution
     *
     * @return string
     */
    function youtube_image($url, $resolution = []) {

        if (!$url)
            return '';

        if (!is_array($resolution) || empty($resolution)) {
            $resolution = [
                'sddefault',
                'hqdefault',
                'mqdefault',
                'default',
                'maxresdefault',
            ];
        }

        $url_img = Url::pixelImg();
        parse_str(parse_url($url, PHP_URL_QUERY), $vars);
        if (isset($vars['v'])) {
            $id = $vars['v'];
            $url_img = 'https://img.youtube.com/vi/' . $id . '/' . $resolution[0] . '.jpg';
        }

        return $url_img;
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'run_closure' ) ) {
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    function run_closure( $value ) {
        if ( $value instanceof \Closure || ( is_array( $value ) && is_callable( $value ) ) ) {
            return call_user_func( $value );
        }

        return $value;
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'if_empty' ) ) {
    /**
     * @param mixed $value
     * @param mixed $fallback
     *
     * @return mixed
     */
    function if_empty( $value, $fallback, bool $strict = false ) {
        $isEmpty = $strict ? empty( $value ) : is_empty( $value );
        return $isEmpty ? $fallback : $value;
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'if_true' ) ) {
    /**
     * @param bool $condition
     * @param mixed $ifTrue
     * @param mixed $ifFalse
     *
     * @return mixed
     */
    function if_true( $condition, $ifTrue, $ifFalse = null ) {
        return $condition ? run_closure( $ifTrue ) : run_closure( $ifFalse );
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'in_range' ) ) {
    /**
     * @param mixed $value
     * @param string|int $min
     * @param string|int $max
     *
     * @return bool
     */
    function in_range( $value, $min, $max ) {
        $inRange = filter_var( $value, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => intval( $min ),
                'max_range' => intval( $max ),
            ]
        ] );

        return false !== $inRange;
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'sanitize_url' ) ) {
    /**
     * @param mixed $value
     *
     * @return string|null
     */
    function sanitize_url( $value ) {
        $url = trim( Cast::toString( $value ) );
        if ( ! Str::startsWith( 'http://, https://', $url ) ) {
            $url = Str::prefix( $url, 'https://' );
        }
        $url = wp_http_validate_url( $url );

        return esc_url_raw( Cast::toString( $url ) );
    }
}

// ------------------------------------------------------

if ( ! function_exists( 'sanitize_input' ) ) {
    /**
     * https://catswhocode.com/php-sanitize-input/
     *
     * @param $key
     * @param array $request
     *
     * @return mixed
     */
    function sanitize_input($key, $request = [] ) {
        if ( isset( $request[ $key ] ) ) {
            return $request[ $key ];
        }

        $variable = filter_input( INPUT_POST, $key );
        if ( is_null( $variable ) && isset( $_POST[ $key ] ) ) {
            $variable = $_POST[ $key ];
        }

        return $variable;
    }
}

// ------------------------------------------------------

if ( ! function_exists( 'sanitize_int' ) ) {
    /**
     * Sanitize integers.
     *
     * @param string $input The value to check.
     *
     * @since 1.0.8
     */
    function sanitize_int( $input ) {
        return Cast::toInt( $input );
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'sanitize_date' ) ) {
    /**
     * If date is invalid then return an empty string.
     *
     * @param mixed $value
     * @param mixed $fallback
     *
     * @return mixed
     */
    function sanitize_date( $value, $fallback = '' ) {
        $date = strtotime( trim( Cast::toString( $value ) ) );
        if ( false !== $date ) {
            return wp_date( 'Y-m-d H:i:s', $date );
        }

        return $fallback;
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'sanitize_bool' ) ) {
    /**
     * @param mixed $value
     *
     * @return bool
     */
    function sanitize_bool( $value ) {
        return Cast::toBool( $value );
    }
}

// ------------------------------------------------------

if ( ! function_exists( 'sanitize_checkbox' ) ) {
    /**
     * Sanitize checkbox values.
     *
     * @param string $checked The value to check.
     *
     * @since 1.0.8
     */
    function sanitize_checkbox( $checked ) {
        // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison -- Intentially loose.
        return isset( $checked ) && true == $checked;
    }
}