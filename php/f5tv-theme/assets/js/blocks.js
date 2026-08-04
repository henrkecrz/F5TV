/**
 * F5TV Theme - Blocks JS
 * Blocos Gutenberg customizados
 */

(function (blocks, element) {
    const { createElement: h } = element;

    blocks.registerBlockType('f5tv/hero-banner', {
        title: 'Hero Banner F5TV',
        icon: 'format-video',
        category: 'f5tv',
        attributes: {
            title: { type: 'string', default: 'AS MELHORES HISTÓRIAS,' },
            subtitle: { type: 'string', default: 'Assista a produções exclusivas.' },
        },
        edit: (props) => {
            const { attributes, setAttributes } = props;
            return h('div', { className: 'f5tv-hero-preview' }, [
                h('h1', { key: 't' }, attributes.title),
                h('p', { key: 's' }, attributes.subtitle),
            ]);
        },
        save: (props) => {
            const { attributes } = props;
            return h('div', { className: 'f5tv-hero-banner' }, [
                h('h1', { key: 't' }, attributes.title),
                h('p', { key: 's' }, attributes.subtitle),
            ]);
        },
    });
})(window.wp.blocks, window.wp.element);
