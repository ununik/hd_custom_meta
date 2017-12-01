<?php
namespace HdCustomMeta;

class CheckboxField extends Field
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

        $data = [];
        if ( isset( $field['options'] ) ) {
            $data = $field['options'];
        }
        ob_start();
        ?>
        <?php
            foreach( $data as  $id => $value ) {
                $checked = '';
                if (in_array($id, $values)){
                    $checked = 'checked';
                }
                ?>
                <input type="checkbox" <?php echo $checked; ?> id="<?php echo $field['id']; ?>_<?php echo $id; ?>" value="<?php echo $id;?>" name="<?php echo $field['id']; ?>[]">
                <lablel for="<?php echo $field['id']; ?>_<?php echo $id; ?>"><?php echo $value; ?></lablel><br>
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