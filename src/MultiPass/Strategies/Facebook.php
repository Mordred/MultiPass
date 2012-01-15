<?php

namespace MultiPass\Strategies;

class Facebook extends \MultiPass\Strategies\OAuth2
{
  const DEFAULT_SCOPE = 'email,offline_access';

  public $name = 'Facebook';

  public function __construct($opts)
  {
    $this->options = array_replace_recursive(array(
        'client_options' => array(
            'site'      => 'https://graph.facebook.com'
          , 'token_url' => '/oauth/access_token'
        )
      , 'token_params' => array(
            'parse' => 'query'
        )
      , 'token_options' => array(
            'header_format' => 'OAuth %s'
          , 'param_name'    => 'access_token'
        )
    ), $opts);

    parent::__construct($this->options);
  }

  public function uid($raw_info = null)
  {
    $raw_info = $raw_info || $this->raw_info();
 
    return $raw_info['id'];
  }

  public function info($raw_info = null)
  {
    $raw_info = $raw_info || $this->raw_info();

    return array(
        'nickname'    => $raw_info['username']
      , 'email'       => $raw_info['email']
      , 'name'        => $raw_info['name']
      , 'first_name'  => $raw_info['first_name']
      , 'last_name'   => $raw_info['last_name']
      , 'image'       => "http://graph.facebook.com/{$raw_info['id']}/picture?type=square"
      , 'description' => $raw_info['bio']
      , 'urls'        => array(
            'Facebook' => $raw_info['link']
          , 'Website'  => isset($raw_info['website']) ? $raw_info['website'] : null
        )
      , 'location'    => isset($raw_info['location']) ? $raw_info['location']['name'] : null
    );
  }
  
  public function extra($raw_info = null)
  {
    $raw_info = $raw_info || $this->raw_info();
    
    return array('raw_info' => $raw_info);
  }

  public function authorizeParams()
  {
    $params = parent::authorizeParams();
    $params['scope'] = $params['scope'] || self::DEFAULT_SCOPE;

    return $params;
  }


  protected function raw_info()
  {
    try {
      $response = $this->token->get('/me', array('parse' => 'json'));
      return $response->parse();
    } catch (\Exception $e) {
      print_r($e);
    }
  }
}