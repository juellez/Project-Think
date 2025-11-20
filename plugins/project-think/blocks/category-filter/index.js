/**
 * Category Filter Block - Editor JavaScript
 */

(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, ToggleControl, SelectControl, Disabled } = wp.components;
    const { __ } = wp.i18n;
    const { ServerSideRender } = wp.serverSideRender || wp.editor;

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

                    // Server-side render preview (shows actual categories)
                    el(
                        Disabled,
                        { key: 'preview' },
                        el(ServerSideRender, {
                            block: 'project-think/category-filter',
                            attributes: attributes
                        })
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
