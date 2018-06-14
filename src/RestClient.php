<?php
namespace Boxtal\BoxtalPhp;

/**
 * @author boxtal <api@boxtal.com>
 * @copyright 2018 Boxtal
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * Class RestClient
 * @package Boxtal\BoxtalPhp
 *
 *  Facilitates REST calls.
 */
class RestClient
{

    /**
     * Access key.
     *
     * @var string
     */
    private $accessKey;

    /**
     * Secret key.
     *
     * @var string
     */
    private $secretKey;

    public static $GET = 'GET';
    public static $POST = 'POST';
    public static $PUT = 'PUT';
    public static $PATCH = 'PATCH';
    public static $DELETE = 'DELETE';

    /**
     * Construct function.
     *
     * @param string $accessKey access key.
     * @param string $secretKey secret key.
     * @void
     */
    public function __construct($accessKey, $secretKey)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    /**
     * API request
     *
     * @param string $method one of GET, POST, PUT, PATCH, DELETE.
     * @param string $url url for the request.
     * @param array $params array of params.
     * @param array $headers array of headers.
     * @return ApiResponse
     */
    public function request($method, $url, $params = array(), $headers = array())
    {

        $headers['Authorization'] = base64_encode($this->accessKey . ':' . $this->secretKey);

        $header = '';
        foreach ($headers as $key => $value) {
            $header .= $key . ': ' . $value ."\r\n";
        }

        $opts = array(
            'http' => array(
                'method'  => $method,
                'header'  => $header,
                'content' => $method !== $this::$GET ? json_encode($params) : null
            )
        );

        $context = stream_context_create($opts);

        if ($method === $this::$GET) {
            if (false === strpos($url, '?')) {
                $url .= '?';
            } else {
                $url .= '&';
            }
            $url .= http_build_query($params);
        }

        $stream = fopen($url, 'r', false, $context);

        $return = new ApiResponse($this->getStreamStatus($stream), stream_get_contents($stream));

        fclose($stream);

        return $return;
    }

    private function getStreamStatus($stream)
    {
        $data = stream_get_meta_data($stream);
        $wrapperLines = $data['wrapper_data'];
        $matches = array();
        for ($i = count($wrapperLines); $i >= 0; $i--) {
            if (0 === strpos($wrapperLines[$i], 'HTTP/1')) {
                preg_match('/(\d{3})/', $wrapperLines[$i], $matches);
                break;
            }
        }
        return empty($matches) ? null : $matches[1];
    }
}
