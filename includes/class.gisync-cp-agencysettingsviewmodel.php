<?php
namespace GISyncCP;

class AgencySettingsViewModel extends GeneralSettingsViewModel
{

    public function disabled_text_field_echo($args)
    {
        echo $this->prepare_input( $args )->disabled()->build();
    }

    public function checkbox_fields_echo($args)
    {
        $builder = new Utils\DOMBuilder();
        $all_values = $this->setting_for_model();

        if (array_key_exists( $args[ 'label_for' ], $all_values )) {
            $checkbox_values = $all_values[ $args[ 'label_for' ] ];
        } else {
            $checkbox_values = array();
        }

        foreach ($args[ 'checkbox_fields' ] as &$field_name) {
            if (!array_key_exists( $field_name, $checkbox_values )) {
                $checkbox_values[ $field_name ] = false;
            }
        }

        ksort( $checkbox_values, SORT_STRING );

        echo $builder->divForCheckboxes()
                     ->withCheckboxElements(
                         $args[ 'label_for' ],
                         $checkbox_values,
                         $this->data[ 'option_name' ] )
                     ->build();
    }
}
