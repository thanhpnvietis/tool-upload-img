<?php
/**
 * @package api_upload_file
 * @version 1.0.0
 */

/*
/*
Plugin Name: api_upload_file
Plugin URI: http://wordpress.org/plugins/api_upload_file/
Description: 
Author: thanhpn
Version: 1.0.0
*/

require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');


add_action('rest_api_init', function () {
  register_rest_route(
    'api_upload_file/v1',
    '/upload-file-to-post/',
    array(
      'methods' => 'POST',
      'callback' => 'uploadFileToPost',
      'body' => array(
        'post_id' => array(
          'required' => true,
        ),
        'files' => array(
          'required' => true,
          'type' => 'array',
          'description' => 'Files to upload.',
          'items' => array(
            'type' => 'object',
            'properties' => array(
              'name' => array(
                'type' => 'string',
              ),
              'type' => array(
                'type' => 'string',
              ),
              'tmp_name' => array(
                'type' => 'string',
              ),
              'size' => array(
                'type' => 'integer',
              ),
            ),
          ),
        ),
      ),
    )
  );
});

function uploadFileToPost($request)
{
  $postId = $request->get_param('post_id');
  $listImage = mypluginMediaUpload($request);
  return array('data'=>pushImageToContent($postId, $listImage));
}

function pushImageToContent($postId, $listImage)
{

  $post = get_post($postId);
  $content = $post->post_content;
  preg_match_all('/<!-- wp:heading -->[\s\S]*?<!-- \/wp:heading -->/', $content, $matchHeading);
  $countHeading = count($matchHeading[0]);
  $offset = 0;
  $limit = ceil(count($listImage)/$countHeading);
  $gallerys = [];

  while ($offset < count($listImage)) {
    $groupImg = array_slice($listImage,$offset,$limit);
    $gallerys[] = getWpGallery($groupImg);
    $offset += $limit;
  }

  $index = 0;
  $content = preg_replace_callback('/<!-- wp:heading -->[\s\S]*?<!-- \/wp:heading -->/',function($matchs) use ($gallerys , &$index){
    $gallery = $gallerys[$index];
    $index++;
    return $matchs[0].$gallery;
  }, $content);
  $post_data = array(
    'ID' => $postId,
    'post_content' => $content,
    'post_status'   => 'publish',
  );

  $updated_post = wp_update_post( $post_data );

  if ( is_wp_error( $updated_post ) ) {
      return new WP_Error( 'post_update_error', $updated_post->get_error_message(), array( 'status' => 500 ) );
  }
  return $listImage;
}

function getWpGallery($images)
{
  
  $tWpImage = "";
  foreach ($images as $key => $value) {
    $image = get_post($value);
    $guid = $image->guid;
    $tWpImage .= "\n";
    $tWpImage .= '<!-- wp:image {"id":'.$value.',"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="'.$guid.'" alt="" class="wp-image-'.$value.'"/></figure>
<!-- /wp:image -->';
  }

    $html = '<!-- wp:gallery {"linkTo":"none"} -->
<figure class="wp-block-gallery has-nested-images columns-default is-cropped">'.$tWpImage.'
</figure>
<!-- /wp:gallery -->';
  return "\n".$html;

}
function mypluginMediaUpload($request)
{
  $time = 'test';
  $attachment_ids = array();
  $files = $_FILES['files'];

  foreach ($files['name'] as $key => $value) {
    if ($files['name'][$key]) {
      $file = array(
        'name' => $files['name'][$key],
        'type' => $files['type'][$key],
        'tmp_name' => $files['tmp_name'][$key],
        'error' => $files['error'][$key],
        'size' => $files['size'][$key]
      );

      $uploaded_file = wp_handle_upload($file, array('test_form' => false), $time);
      if (isset($uploaded_file['error'])) {
        return new WP_Error('upload_error', $uploaded_file['error']);
      }

      $attachment = array(
        'post_mime_type' => $uploaded_file['type'],
        'post_title' => preg_replace('/\.[^.]+$/', '', basename($uploaded_file['file'])),
        'post_content' => '',
        'post_status' => 'inherit',
        'guid' => $uploaded_file['url'],
      );
      $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
      wp_update_attachment_metadata($attachment_id, $attachment_data);
      $attachment_ids[] = $attachment_id;
    }
  }

  return $attachment_ids;
}
