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
          // 'required' => true,
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
  $imageId = $request->get_param('list_image_id');
  // $removeAttatchId = explode(',',$request->get_param('delete_attachment_id') ?? '') ;
  if($imageId){
    $listImage = explode(',',$imageId);
  }else{
    $listImage = mypluginMediaUpload($request);
  }
  pushImageToContent($postId, $listImage,$request);
  set_post_thumbnail( $postId, $listImage[rand(0, 1)] ?? $listImage[0]);
  // deleteAttachment($removeAttatchId);
  $listImage = array_map(function($id){
    return get_post($id);
  },$listImage);
  return array('data'=>$listImage);
}

function deleteAttachment($idAttachment){
  foreach ($idAttachment as $key => $value) {
    wp_delete_attachment($value,true);
  }
}

function pushImageToContent($postId, $listImage, $request)
{
  $post = get_post($postId);
  $content = $post->post_content;

  //check number H tag
  preg_match_all('/<!-- wp:heading.*?-->[\s\S]*?<!-- \/wp:heading -->/', $content, $matchHeading);
  $countHeading = count($matchHeading[0]);

  if(!$countHeading){
    return [];
  }
  $gallerys = [];

  $linkTo = $request->get_param('link_to') ;
  $size = $request->get_param('size') ;
  $columns = $request->get_param('columns') ;
  $attribute = [
    'linkTo' => $linkTo ? $linkTo : 'none',
    'sizeSlug' => $size ? $size : 'large',
    'columns' => $columns ? $columns : 'default',
  ];
  //add 2 image to first H tag
  $attributeFirst = $attribute;
  $groupImgFirst = array_slice($listImage,0,2);
  $listImage = array_slice($listImage,2);
  $attributeFirst['columns'] = 2;
  $gallerys[] = getWpGallery($groupImgFirst,$attributeFirst);

  //add image to other heading
  $offset = 0;
  $limit = ceil(count($listImage)/($countHeading - 1));
  while ($offset < count($listImage)) {
    $groupImg = array_slice($listImage,$offset,$limit);
    $gallerys[] = getWpGallery($groupImg,$attribute);
    $offset += $limit;
  }

  $index = 0;

  //remove wp:gallery old before H tag
  $content = preg_replace(
    '/(:?<!-- wp:gallery.*-->[\s\S]*?<!-- \/wp:gallery -->[\n ]*?)(<!-- wp:heading.*?-->[\s\S]*?<!-- \/wp:heading -->[\n ]*?)/',
    '$2',
    $content
  );

  // add old wp:gallery before H tag
  $content = preg_replace_callback('/<!-- wp:heading.*?-->[\s\S]*?<!-- \/wp:heading -->/',function($matchs) use ($gallerys , &$index){
    $gallery = $gallerys[$index];
    $index++;
    return $gallery.$matchs[0];
  }, $content);
  $post_data = array(
    'ID' => $postId,
    'post_content' => $content,
    'post_status'   => 'publish',
  );
  remove_action( 'post_updated', 'wp_save_post_revision' );
  $updated_post = wp_update_post( $post_data );
  add_action( 'post_updated', 'wp_save_post_revision' );
  if ( is_wp_error( $updated_post ) ) {
      return new WP_Error( 'post_update_error', $updated_post->get_error_message(), array( 'status' => 500 ) );
  }
  return $listImage;
}
function getWpGallery($images,$attribute)
{
  $attributeStr = json_encode($attribute); 
  $tWpImage = "";
  foreach ($images as $key => $value) {
    $pathImage = wp_get_attachment_image_src($value,$attribute['sizeSlug']);
    $tWpImage .= "\n";
    $tWpImage .= '<!-- wp:image {"id":'.$value.',"sizeSlug":"'.$attribute['sizeSlug'].'","linkDestination":"'. $attribute['linkTo'].'"} -->
<figure class="wp-block-image size-'.$attribute['sizeSlug'].'"><img src="'.$pathImage[0].'" alt="" class="wp-image-'.$value.'"/></figure>
<!-- /wp:image -->';
  }

  $html = '<!-- wp:gallery '.$attributeStr.' -->
<figure class="wp-block-gallery has-nested-images columns-'.$attribute['column'].' is-cropped">'.$tWpImage.'
</figure>
<!-- /wp:gallery -->';
  return "\n".$html;
}
function mypluginMediaUpload($request)
{
  $postId = $request->get_param('post_id');
  $folderName = $request->get_param('folder_name') ?? $postId;
  $attachment_ids = array();
  $files = $_FILES['files'];
  if(!$files){
    return [];
  }
  add_filter( 'upload_dir', 'custom_upload_dir' );
  foreach ($files['name'] as $key => $value) {
    if ($files['name'][$key]) {
      $file = array(
        'name' => $files['name'][$key],
        'type' => $files['type'][$key],
        'tmp_name' => $files['tmp_name'][$key],
        'error' => $files['error'][$key],
        'size' => $files['size'][$key]
      );

      $uploaded_file = wp_handle_upload($file, array('test_form' => false));
      if (isset($uploaded_file['error'])) {
        return new WP_Error('upload_error', $uploaded_file['error']);
      }
      if($uploaded_file['url']){
        // replace [//] to [/]
        $uploaded_file['url'] = preg_replace('/(?<!https:|http:)\/\//','/',$uploaded_file['url']);
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
  remove_filter( 'upload_dir', 'custom_upload_dir' );
  return $attachment_ids;
}

function custom_upload_dir( $dirs ) {
  
  $postId = $_POST['post_id'] ?? '';
  $folderName = $_POST['folder_name'] ?? '';
  if(!$folderName){
    $folderName = $postId;
  }
  $folderName = mb_convert_encoding($folderName, 'ASCII', 'UTF-8');
  $folderName = preg_replace( '/[^a-z0-9]+/', '-', strtolower( $folderName ) );
  $dirs['subdir'] = '/'.$folderName;
  $dirs['path'] = $dirs['basedir'] . '/'.$folderName;
  $dirs['url'] = $dirs['baseurl'] . '/'.$folderName;
  return $dirs;
}

