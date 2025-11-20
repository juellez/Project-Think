/**
 * Category Filter Block - Editor JavaScript
 */

(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, ToggleControl, SelectControl } = wp.components;
    const { __ } = wp.i18n;

    registerBlockType('project-think/category-filter', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { showAllOption, displayStyle, taxonomy } = attributes;

            const blockProps = useBlockProps({
                className: 'project-category-filter-editor'
            });

            return el(
                'div',
                blockProps,
                [
                    // Inspector controls (sidebar)
                    el(
                        InspectorControls,
                        { key: 'inspector' },
                        el(
                            PanelBody,
                            {
                                title: __('Filter Settings', 'project-think'),
                                initialOpen: true
                            },
                            [
                                el(SelectControl, {
                                    key: 'display-style',
                                    label: __('Display Style', 'project-think'),
                                    value: displayStyle,
                                    options: [
                                        { label: __('Buttons', 'project-think'), value: 'buttons' },
                                        { label: __('Dropdown', 'project-think'), value: 'dropdown' },
                                        { label: __('List', 'project-think'), value: 'list' }
                                    ],
                                    onChange: function (value) {
                                        setAttributes({ displayStyle: value });
                                    }
                                }),
                                el(ToggleControl, {
                                    key: 'show-all',
                                    label: __('Show "All Projects" option', 'project-think'),
                                    checked: showAllOption,
                                    onChange: function (value) {
                                        setAttributes({ showAllOption: value });
                                    }
                                }),
                                el(SelectControl, {
                                    key: 'taxonomy',
                                    label: __('Taxonomy', 'project-think'),
                                    value: taxonomy,
                                    options: [
                                        { label: __('Categories/Subjects', 'project-think'), value: 'category' },
                                        { label: __('Tags', 'project-think'), value: 'post_tag' }
                                    ],
                                    onChange: function (value) {
                                        setAttributes({ taxonomy: value });
                                    },
                                    help: __('Choose which taxonomy to filter by', 'project-think')
                                })
                            ]
                        )
                    ),

                    // Block preview in editor
                    el(
                        'div',
                        {
                            key: 'preview',
                            className: 'project-category-filter-preview',
                            style: {
                                padding: '20px',
                                border: '2px dashed #ccc',
                                borderRadius: '4px',
                                background: '#f9f9f9'
                            }
                        },
                        [
                            el('p', {
                                key: 'title',
                                style: {
                                    margin: '0 0 10px 0',
                                    fontWeight: 'bold',
                                    fontSize: '14px'
                                }
                            }, __('Project Category Filter', 'project-think')),
                            el('p', {
                                key: 'desc',
                                style: {
                                    margin: '0 0 15px 0',
                                    fontSize: '12px',
                                    color: '#666'
                                }
                            }, __('Display Style: ', 'project-think') + displayStyle),
                            el('div', {
                                key: 'sample',
                                style: {
                                    padding: '10px',
                                    background: 'white',
                                    borderRadius: '3px'
                                }
                            }, [
                                displayStyle === 'dropdown'
                                    ? el('select', {
                                        key: 'dropdown',
                                        style: { width: '100%', padding: '8px' },
                                        disabled: true
                                    }, [
                                        showAllOption && el('option', { key: 'all' }, __('All Projects', 'project-think')),
                                        el('option', { key: 'cat1' }, __('Sample Category 1', 'project-think')),
                                        el('option', { key: 'cat2' }, __('Sample Category 2', 'project-think'))
                                    ])
                                    : el('ul', {
                                        key: 'list',
                                        style: {
                                            listStyle: 'none',
                                            margin: 0,
                                            padding: 0,
                                            display: displayStyle === 'buttons' ? 'flex' : 'block',
                                            gap: displayStyle === 'buttons' ? '10px' : '5px',
                                            flexWrap: 'wrap'
                                        }
                                    }, [
                                        showAllOption && el('li', {
                                            key: 'all',
                                            style: displayStyle === 'buttons' ? {
                                                padding: '8px 16px',
                                                background: '#1a4548',
                                                color: 'white',
                                                borderRadius: '4px',
                                                cursor: 'not-allowed'
                                            } : { marginBottom: '5px' }
                                        }, __('All Projects', 'project-think')),
                                        el('li', {
                                            key: 'cat1',
                                            style: displayStyle === 'buttons' ? {
                                                padding: '8px 16px',
                                                background: '#f0f0f0',
                                                borderRadius: '4px',
                                                cursor: 'not-allowed'
                                            } : { marginBottom: '5px' }
                                        }, __('Sample Category 1', 'project-think')),
                                        el('li', {
                                            key: 'cat2',
                                            style: displayStyle === 'buttons' ? {
                                                padding: '8px 16px',
                                                background: '#f0f0f0',
                                                borderRadius: '4px',
                                                cursor: 'not-allowed'
                                            } : { marginBottom: '5px' }
                                        }, __('Sample Category 2', 'project-think'))
                                    ])
                            ])
                        ]
                    )
                ]
            );
        },

        save: function () {
            // Dynamic block - no save needed
            return null;
        }
    });
})(window.wp);
