export default {
    components: [
        { label: "Checkbox", type: "checkbox" },
        { label: "Text field", type: "text", },
        { label: "Textarea", type: "textarea" },
        {
            label: 'Select',
            type: 'select',
            multiple: false,
            values: [
                {
                    label: "Option 1",
                    value: "option-1",
                    selected: false
                },
                {
                    label: "Option 2",
                    value: "option-2",
                    selected: false
                },
                {
                    label: "Option 3",
                    value: "option-3",
                    selected: false
                }
            ]
        },
        
        // {
        //     label: "Checkbox group",
        //     type: "checkbox-group",
        //     values: [
        //         {
        //             label: "Option 1",
        //             value: "option-1",
        //             selected: false
        //         },
        //         {
        //             label: "Option 2",
        //             value: "option-2",
        //             selected: false
        //         },
        //         {
        //             label: "Option 3",
        //             value: "option-3",
        //             selected: false
        //         }
        //     ]
        // },
        // {
        //     label: "Radio group",
        //     type: "radio-group",
        //     values: [
        //         {
        //             label: "Option 1",
        //             value: "option-1",
        //             selected: false
        //         },
        //         {
        //             label: "Option 2",
        //             value: "option-2",
        //             selected: false
        //         },
        //         {
        //             label: "Option 3",
        //             value: "option-3",
        //             selected: false
        //         }
        //     ]
        // },
        // {
        //     label: "File input",
        //     type: "file",
        //     multiple: false
        // },
        // {
        //     label: "Date field",
        //     type: "date",
        // },
        // {
        //     label: "Switch field",
        //     type: "switch",
        //     labelActive: 'Active',
        //     labelInactive: 'Inactive'
        // },
        // {
        //     label: "Range Field",
        //     type: "range",
        //     values: {
        //         min:0,
        //         max:100
        //     }
        // },
    ],
}