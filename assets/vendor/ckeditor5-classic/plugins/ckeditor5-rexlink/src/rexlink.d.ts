/**
 * @module RexLink/RexLink
 */
import { Plugin, type Editor } from '@ckeditor/ckeditor5-core';
import LinkUI from '@ckeditor/ckeditor5-link/src/linkui';
import type LinkFormView from '@ckeditor/ckeditor5-link/src/ui/linkformview';
import type { ViewWithCssTransitionDisabler } from '@ckeditor/ckeditor5-ui';
export default class RexLink extends Plugin {
    #private;
    readonly editor: Editor;
    static get pluginName(): 'RexLink';
    /**
     * @inheritDoc
     */
    static get requires(): readonly [typeof LinkUI];
    /**
     * The form view displayed inside the balloon.
     */
    linkFormView: LinkFormView & ViewWithCssTransitionDisabler | null;
    constructor(editor: Editor);
    init(): void;
}
