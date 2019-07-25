<?php

namespace Drupal\tr_form_examples\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
/**
 * Implements an example form.
 */
class ExampleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'example_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {   
    // This container wil be replaced by AJAX.

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#description' => $this->t('Add your name '),
      '#maxlength' => 64,
      '#size' => 64,
      '#required' => TRUE
    ];
    $form['identification'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Indetification'),
      '#description' => $this->t('Add the identification'),
      '#maxlength' => 64,
      '#size' => 64,
      '#required' => TRUE
    ];    
    $form['date_birthday'] = [
      '#type' => 'date',
      '#title' => $this->t('Birthday'),
      '#description' => $this->t('Birthday'),
      '#maxlength' => 64,
      '#size' => 64,
      '#required' => TRUE
    ];    
    $form['rol'] = [
      '#type' => 'select',
      '#options' => array(
        'admin' => 'Administrador',
        'webm' => 'Webmaster',
        'developer' => 'Desarrollador',                
        ),
      '#title' => $this->t('Rol'),
    ];  

    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#ajax' => [
        'callback' => '::promptCallback',
        'wrapper' => 'box-container',
      ],
    ];
    $form['container'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'box-container'],
    ];
    // The box contains some markup that we can change on a submit request.
    $form['container']['response'] = [
      '#type' => 'markup',
      '#markup' => '<h1></h1>',
    ]; 

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $name = $form_state->getValue('name');
    $Id = $form_state->getValue('identification');

    if (strlen($name) < 10) {
      // Set an error for the form element with a key of "name".
      $form_state->setErrorByName('name', $this->t('The name must be at least 10 characters long.'));
    }
    if (!is_numeric($Id)) {
      // Set an error for the form element with a key of "name".
      $form_state->setErrorByName('identification', $this->t('The identification must be at only numbers'));
    }    
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }
  /**
   * Callback for submit_driven example.
   *
   * Select the 'box' element, change the markup in it, and return it as a
   * renderable array.
   *
   * @return array
   *   Renderable array (the box element)
   */
  public function promptCallback(array &$form, FormStateInterface $form_state) {
    if(empty($form_state->getErrors())){
      $dataInsert = array();
      foreach ($form_state->getValues() as $key => $value) {
        if(strpos($key ,'form') === FALSE  && strpos($key ,'op') === FALSE && strpos($key ,'submit') === FALSE){
          if(strpos($key ,'rol') !== FALSE && $value == 'admin'){
            $dataInsert[$key] = $value;
            $dataInsert['status'] = 1;
          }else{
            $dataInsert[$key] = $value;
          }
        }      
      }
      $dataInsert['create'] = time();
      $connection = \Drupal\Core\Database\Database::getConnection();
      $db_name = 'example_users';
      $id = $connection->insert($db_name)
              ->fields($dataInsert)->execute();   
      $dataInsert['id'] =$id;
      \Drupal::logger('example_users')->notice("Datos Guardados".strval(json_encode( $dataInsert)));   
      $element = $form['container'];
      $element['box']['#markup'] ="Datos Guardados ID: ".$id;
      return $element;          
    }
   
    $element = $form['container'];
    $element['box']['#markup'] = parent::validateForm($form, $form_state);
    return $element;
  }
}