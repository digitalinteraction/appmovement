<?php
App::uses('AppController', 'Controller');
/**
 * TableTranslations Controller
 *
 * @property TableTranslation $TableTranslation
 * @property PaginatorComponent $Paginator
 */
class TableTranslationsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array();

	public $uses = array('TableTranslation');

/**
 * index method
 *
 * @return void
 */
	public function generate() {
		$this->autoRender = false;
        $this->layout = 'ajax';


        $translatables = $this->TableTranslation->find('all');

		$translation_file = new File(ROOT . DS . APP_DIR . '/Locale/default.pot', false, 0775);

		$language_file_contents = $translation_file->read(true, 'r+');

		$translation_strings_to_append = "";

        // load the models and load fields from translatable
        foreach($translatables as $translatable)
        {
        	$this->loadModel($translatable['TableTranslation']['model']);

        	// fetch all rows of the tabe being translated
        	$rows = $this->$translatable['TableTranslation']['model']->find('all', array(
        																'conditions' => array(
        																	'not' => array(
        																		$translatable['TableTranslation']['field'] => null
        																	)
        																)
        															));

        	// foreach dynamic row in the table being translated, add the field to the translations file
        	foreach($rows as $row)
        	{
        		$field = $row[$translatable['TableTranslation']['model']][$translatable['TableTranslation']['field']];

        		$translation_string = "msgid \"" . $field . "\"";

        		// check that field doesn't already exist in the POT file
        		$existing_string = strpos($language_file_contents, $translation_string);

        		// if there is not an existing string then add it
        		if($existing_string === false)
        		{
        			// yes those horrible backslashes are required so we can use the \n :)
	        		$new_translation = "\n#: From Database Table: " . $translatable['TableTranslation']['model'] . "[" . $translatable['TableTranslation']['field'] . "]\n";
	        		$new_translation .= "msgid \"" . $field . "\"\n";
	        		$new_translation .= "msgstr \"\"\n";

	        		$translation_strings_to_append .= $new_translation;
        		}
        	}
        }

        echo '#### Appended Translations from Database Have Been Written to file: ' . ROOT . DS . APP_DIR . '/Locale/default.pot ####';
        echo "\n";

        echo '<pre>';
        print_r($translation_strings_to_append);
        echo '</pre>';

        $translation_file->append($translation_strings_to_append);
        $translation_file->close();
	}
}
