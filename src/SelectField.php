<?php
namespace HdCustomMeta;

class SelectField extends Field
{
    protected $_content = '';

    public function __construct( $field )
    {
        global $post;

        if ( !isset( $field['id'] ) ) {
            return;
        }

        $value = get_post_meta( $post->ID, $field['id'], true );

        if ($value && $value!='') {
            $values = json_decode($value);
        } else {
            $values = [];
        }

        $multiple = false;
        if(isset($field['multiple']) && $field['multiple']) {
            $multiple = true;
        }

        $data = [];
        if ( isset( $field['options'] ) ) {
            $data = $field['options'];
        }
        ob_start();
        ?>
        <select id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?><?php if($multiple): echo '[]'; endif; ?>" class="text_field" <?php if($multiple): echo 'multiple="multiple"'; endif; ?>>
            <?php
            foreach( $data as  $id => $value ) {
                $selected = '';
                if (in_array($id, $values)){
                    $selected = 'selected';
                }
                ?>
                    <option <?php echo $selected;?> value="<?php echo $id; ?>"><?php echo $value; ?></option>
                <?php
            }
            ?>
        </select>
        <?php
        $this->_content = ob_get_clean();
    }

    public function output()
    {
        return $this->_content;
    }

    public function fieldProtection()
    {
        return ['multiple_select'];
    }
}