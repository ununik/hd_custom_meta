<?php
namespace HdCustomMeta;

class CodeField extends Field
{
    protected $_content = '';

    public function __construct( $field )
    {
        global $post;

        if ( !isset( $field['id'] ) ) {
            return;
        }

        $value = get_post_meta( $post->ID, $field['id'], true );
        $code_mode = '';
        if (isset($field['mode'])) {
            switch ($field['mode']) {
                case 'javascript':
                    $code_mode = 'javascript';
                    break;
                case 'php':
                    $code_mode = 'php';
                    break;
                case 'css':
                    $code_mode = 'css';
                    break;
            }
        }
        ob_start();
        ?>
        <textarea id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" style="display: none;"><?php echo $value; ?></textarea>

        <div class="code_editor">
        <div id="editor_<?php echo $field['id']; ?>" data-id="<?php echo $field['id']; ?>" class="code_editor_content" style="position: absolute; top:0px; left: 0px; right: 0px; bottom: 0px;"><?php echo $value; ?></div>
        </div>


        <script>
            var editor = ace.edit("editor_<?php echo $field['id']; ?>");
            editor.setTheme("ace/theme/monokai");
            editor.getSession().on('change', function(e) {
                $('#<?php echo $field['id']; ?>').val(editor.getValue());
            });
            <?php if ($code_mode != '') { ?>
            editor.getSession().setMode("ace/mode/<?php echo $code_mode; ?>");
            <?php } ?>
        </script>
        <?php
        $this->_content = ob_get_clean();
    }

    public function output()
    {
        return $this->_content;
    }

    public function fieldProtection()
    {
        return [];
    }
}