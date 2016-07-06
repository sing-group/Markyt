<?php

App::uses('AppModel', 'Model');

/**
 * User Model
 *
 * @property Group $Group
 * @property Annotation $Annotation
 * @property Project $Project
 * @property Round $Round
 */
class User extends AppModel {

    var $actsAs = array('Containable');
    public $virtualFields = array("full_name" => "CONCAT(username,' ' ,surname)");
    public $displayField = 'full_name';

    public function beforeSave($options = array()) {

        if (!empty($this->data['User']['image'])) {
            if ($this->data['User']['image']['size'] != 0) {
                $fileData = $this->resizeImage(512, $this->data['User']['image']['tmp_name']);
                if (!isset($fileData)) {
                    $fileData = fread(fopen($this->data['User']['image']['tmp_name'], 'r'), $this->data['User']['image']['size']);
                }

//            debug($fileData);
//            throw new Exception;

                $this->data['User']['image_type'] = $this->data['User']['image']['type'];
                $this->data['User']['image'] = $fileData;
            } else {
                $this->data['User']['image'] = null;
                $this->data['User']['image_extension'] = null;
            }
        }

        //App::import('Component','Auth');
        //$AuthComponent = new AuthComponent(new ComponentCollection);
        //AuthComponent->password($this->data['User']['password'])
        if (isset($this->data[$this->alias]['password']) && strlen(trim($this->data[$this->alias]['password'])) != 0) {
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data['User']['password']);
        } else {
            unset($this->data['User']['password']);
        }
        return true;
    }

    public function isImage($field = array(), $name_field = null) {
        if ((!empty($this->data['User']['image']['type']) && strpos($this->data['User']['image']['type'], 'image') === true ||
                strpos($this->data['User']['image']['type'], 'bmp') === false) && (number_format($this->data['User']['image']['size'] / 1048576, 2) < 8 || $this->data['User']['image']['size'] == 0 )) {
            return true;
        } else {
            return false;
        }
    }

    // Get the file from URL by cURL if installed, else by file_get_contents() function
    public function resizeImage($newWidth, $originalFile) {
        $allowGif = Configure::read('allowGif');
        if (!$allowGif) {
            $info = getimagesize($originalFile);
            $mime = $info['mime'];
            switch ($mime) {
                case 'image/jpeg':
                    $image_create_func = 'imagecreatefromjpeg';
                    $image_save_func = 'imagejpeg';
                    $new_image_ext = 'jpg';
                    break;

                case 'image/png':
                    $image_create_func = 'imagecreatefrompng';
                    $image_save_func = 'imagepng';
                    $new_image_ext = 'png';
                    break;

                case 'image/gif':
                    if (!$allowGif) {
                        $image_create_func = 'imagecreatefromgif';
                        $image_save_func = 'imagegif';
                        $new_image_ext = 'gif';
                    }
                    break;
                default:
                //throw Exception('Unknown image type.');
            }
            if (isset($image_save_func) && function_exists('imap_open')) {
                $img = $image_create_func($originalFile);
                list($width, $height) = getimagesize($originalFile);

                $newHeight = ($height / $width) * $newWidth;
                $tmp = imagecreatetruecolor($newWidth, $newHeight);
                $targetFile = $originalFile;

                imageAlphaBlending($tmp, false);
                imageSaveAlpha($tmp, true);

                imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                if (file_exists($targetFile)) {
                    unlink($targetFile);
                }
                ob_start(); //Start output buffer.
                $image_save_func($tmp); //This will normally output the image, but because of ob_start(), it won't.
                $contents = ob_get_contents(); //Instead, output above is saved to $contents
                ob_end_clean(); //End the output buffer.
            } else {
                $contents = null;
            }
        }
        //$image_save_func($tmp, "$targetFile.$new_image_ext");
        return $contents;
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'group_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'This field cannot be empty',
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'username' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'This field cannot be empty',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
            'noJokeName' => array(
                'rule' => array('validateNameJoke', 'username'),
                'message' => 'Invalid username',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
            'inUse' => array('rule' => array('isUnique', 'username'), 'message' => 'This user name is already in use please choose another')
        ),
        'surname' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'This field cannot be empty',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
//        'subject' => array(
//            'notempty' => array(
////                'rule' => array('notempty'),
////                'message' => 'This field cannot be empty',
//            'allowEmpty' => true,
//            'required' => true,
//            //'last' => false, // Stop validation after this rule
//            //'on' => 'create', // Limit validation to 'create' or 'update' operations
//            ),
//        ),
        'email' => array(
            'email' => array(
                'rule' => array('email'),
                'message' => 'This email is incorrect Example:user@mail.com'
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
            'inUse' => array(
                'rule' => array('isUnique', 'email'),
                'message' => 'This email is already in use please choose another')
        ),
        'password' => array(
            'length' => array(
                'rule' => array('minLength', '6'),
                'required' => true,
                'allowEmpty' => true,
                'message' => 'Password must have at least 6 characters'),
        ),
        'image' => array(
            'rule' => array('isImage'),
            'allowEmpty' => true,
            'message' => 'Image extension is not valid. Try with images gif, jpeg, png, jpg and size <8MB'),
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'group_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'Annotation' => array(
            'className' => 'Annotation',
            'foreignKey' => 'user_id',
            'dependent' => TRUE,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Connection' => array(
            'className' => 'Connection',
            'foreignKey' => 'user_id',
            'dependent' => TRUE,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Post' => array(
            'className' => 'Post',
            'foreignKey' => 'user_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'AnnotatedDocument' => array(
            'className' => 'AnnotatedDocument',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => true
        ),
        'Job' => array(
            'className' => 'Job',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => true
        ),
    );

    /**
     * hasAndBelongsToMany associations
     *
     * @var array
     */
    public $hasAndBelongsToMany = array(
        'Project' => array(
            'className' => 'Project',
            'joinTable' => 'projects_users',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'project_id',
            'unique' => 'keepExisting',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => ''
        ),
        'Round' => array(
            'className' => 'Round',
            'joinTable' => 'users_rounds',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'round_id',
            'unique' => 'keepExisting',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => ''
        )
    );

}
