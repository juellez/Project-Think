// blocks/project-think-field/project-field.js

const { registerBlockType } = wp.blocks;
const { SelectControl, PanelBody } = wp.components;
const { InspectorControls } = wp.blockEditor;
const { createElement } = wp.element; // <-- NEW: Import createElement
const { __ } = wp.i18n;

// Function to find the label text for the placeholder
function getLabelForMetaField(metaField, options) {
    const option = options.find(opt => opt.value === metaField);
    return option ? option.label : 'None Selected';
}

registerBlockType('project-think/project-field', {
    edit: (props) => {
        const { attributes: { metaField }, setAttributes } = props;

        const options = [
            { label: __('Unit Name (short text)', 'project-think'), value: '_project_think_unit_name' },
            { label: __('Overview (brief text)', 'project-think'), value: '_project_think_overview' },
            { label: __('Culminates (brief text)', 'project-think'), value: '_project_think_culminates' },
            { label: __('Project Google Doc Link', 'project-think'), value: '_project_think_google_doc_link' },
            { label: __('Example Link', 'project-think'), value: '_project_think_example_link' },
            { label: __('Unit Outline Link', 'project-think'), value: '_project_think_unit_outline_link' },
        ];
        
        // 1. Create the Controls Panel (using createElement instead of JSX)
        const controls = createElement(InspectorControls, {},
            createElement(PanelBody, { title: __('Field Selection', 'project-think') },
                createElement(SelectControl, {
                    label: __('Select Custom Field:', 'project-think'),
                    value: metaField,
                    options: options,
                    onChange: (newMetaField) => setAttributes({ metaField: newMetaField })
                })
            )
        );
        
        // 2. Create the Editor Placeholder (using createElement instead of JSX)
        const placeholder = createElement('div', {
            style: { padding: '10px', border: '1px solid #ccc', backgroundColor: '#f9f9f9' }
        },
            // Note: We are using string concatenation for simplicity here, but could use more createElement calls
            'Project Think Field Block: Displaying: ' + getLabelForMetaField(metaField, options)
        );

        // 3. Return the array of elements (replaces the React Fragment <>)
        return [controls, placeholder]; 
    },
    
    save: () => {
        return null; // Dynamic block, no content saved to post
    },
});