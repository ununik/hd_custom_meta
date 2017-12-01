<?php
namespace HdCustomMeta;

class CustomPostType
{
    protected $_url = '';
    protected $_cpt_id = null;
    protected $_save_ids = [];
    /**
     * CustomPostType constructor.
     * @param (string) $cpt_id
     * @param (array) $settings
     */
    public function __construct( $cpt_id, $settings )
    {
        $this->_cpt_id = register_post_type( $cpt_id, $settings );

        add_action( 'add_meta_boxes', array( $this, 'customMetaBox' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'adminStyles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'adminScripts' ) );
        add_action( 'save_post',  array( $this, 'saveCustomFields' ), 1, 2 );

        $this->_url = plugin_dir_url( __FILE__ );
    }

    public function addMetaBox( $id, $title, $args = [] )
    {
        $metabox = new \StdClass();
        $metabox->id = $id;
        $metabox->title = $title;
        $metabox->args = $args;

        $this->_metaboxes[] = $metabox;
    }

    public function customMetaBox()
    {
        foreach ($this->_metaboxes as $metabox) {
            add_meta_box( $metabox->id, $metabox->title, array( $this, 'customBoxHtml'), $this->_cpt_id->name, 'normal', 'default' , array($metabox) );
        }
    }

    public function customBoxHtml( $post, $args )
    {
        if ( !isset($args['args'][0]) ) {
            return;
        }

        $args = $args['args'][0];
        $metabox_id = count( $this->_metaboxes );
        if ( isset( $args->id ) ) {
            $metabox_id = $args->id;
        }
        $tabs = [];
        if ( isset( $args->args['tabs'] ) ) {
            $tabs = $args->args['tabs'];
        }
?>
        <div class="content_tabs">
            <div class="tab">
                <?php foreach ($tabs as $tab): ?>
                    <div class="tablinks" data-target="<?php echo $metabox_id; ?>_<?php echo $tab['id'] ?>"><?php echo $tab['label'] ?></div>
                <?php endforeach; ?>
            </div>

            <?php foreach ($tabs as $tab): ?>
                <div id="<?php echo $metabox_id; ?>_<?php echo $tab['id'] ?>" class="tabcontent">
                    <table class="content_tabs_table">
                    <?php
                    if ( isset( $tab['fields'] ) ) :
                        foreach ($tab['fields'] as $field): ?>
                            <tr>
                                <td class="label_and_description">
                                    <?php
                                        if ( isset( $field['label'] ) ) {
                                            echo '<label ';
                                            if ( isset( $field['id'] ) ) {
                                                echo 'for="'.$field['id'].'"';
                                            }
                                            echo '>' . $field['label'] . '</label>';
                                        }
                                        if ( isset( $field['description'] ) ) {
                                            echo '<p>' . $field['description'] . '</p>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $fieldClass = $this->generateField( $field );
                                    if ($fieldClass) {
                                        echo $fieldClass->output();
                                    } ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
<?php
    }

    public function adminStyles()
    {
        wp_enqueue_style('post_type', $this->_url.'/styles/main.css');
        wp_enqueue_style('timepicker', $this->_url.'/scripts/timepicker/jquery.timepicker.min.css');
        wp_enqueue_style( 'wp-color-picker' );
        wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
        wp_enqueue_style('jquery-ui');
    }

    public function adminScripts()
    {

        wp_enqueue_script("jquery", '');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('post_type_tabs', $this->_url.'/scripts/tabs.js', array('jquery', 'wp-color-picker'), true);
        wp_enqueue_script('syntax_highlight', $this->_url.'/scripts/ace/ace.js', array('jquery'), true);
        wp_enqueue_script( 'timepicker', $this->_url.'/scripts/timepicker/jquery.timepicker.min.js', array( 'jquery') );
    }

    public function saveCustomFields( $post_id, $post )
    {
        $save = [];
        $data = [];
        foreach( $this->_metaboxes as $metabox ) {
          foreach ( $metabox->args['tabs'] as $tab ) {
              foreach ( $tab['fields'] as $field ) {
                  if ( isset( $field['id'] ) ) {
                      $data[$field['id']] = $this->generateField( $field )->fieldProtection();
                  }
                  if ( $field['type'] == 'custom_text' ) {
                      if ( isset( $field['save_fields'] ) ) {
                          foreach ( $field['save_fields']  as $save_field ) {
                              $data[$save_field] = [];
                          }
                      }
                  }
              }
          }
        }
        foreach( $data as $id => $protections ) {
            if ( isset( $_POST[$id] ) ) {
                $value = $_POST[$id];
                foreach ($protections as $protection) {
                    $value = Field::protectedSave($protection, $value);
                }

                $save[$id] = $value;
            }
        }

        // Add values of $events_meta as custom fields
        foreach ( $save as $key => $value ) { // Cycle through the $events_meta array!
            if ( $post->post_type == 'revision' ) {
                return;
            } // Don't store custom data twice

            $value = implode( ',', (array) $value ); // If $value is an array, make it a CSV (unlikely)
            if( get_post_meta( $post->ID, $key, false ) ) { // If the custom field already has a value
                update_post_meta( $post->ID, $key, $value );
            } else { // If the custom field doesn't have a value
                add_post_meta( $post->ID, $key, $value );
            }

            if ( !$value ) {
                delete_post_meta( $post->ID, $key );
            } // Delete if blank
        }
    }

    protected function generateField( $field )
    {
        if ( isset( $field['type'] ) ) {
            switch ( $field['type'] ) {
                case 'text':
                    $fieldClass = new TextField( $field );
                    return $fieldClass;
                    break;
                case 'inteager':
                    $fieldClass = new InteagerField( $field );
                    return $fieldClass;
                    break;
                case 'email':
                    $fieldClass = new EmailField( $field );
                    return $fieldClass;
                    break;
                case 'select':
                    $fieldClass = new SelectField( $field );
                    return $fieldClass;
                    break;
                case 'checkbox':
                    $fieldClass = new CheckboxField( $field );
                    return $fieldClass;
                    break;
                case 'radio':
                    $fieldClass = new RadioField( $field );
                    return $fieldClass;
                    break;
                case 'code':
                    $fieldClass = new CodeField( $field );
                    return $fieldClass;
                    break;
                case 'color':
                    $fieldClass = new ColorField( $field );
                    return $fieldClass;
                    break;
                case 'date':
                    $fieldClass = new DateField( $field );
                    return $fieldClass;
                    break;
                case 'time':
                    $fieldClass = new TimeField( $field );
                    return $fieldClass;
                    break;
                case 'date_time':
                    $fieldClass = new DateTimeField( $field );
                    return $fieldClass;
                    break;
                case 'custom_text':
                    $fieldClass = new CustomTextField( $field );
                    return $fieldClass;
                    break;
                default:
                    return false;
            }
        }
    }
}