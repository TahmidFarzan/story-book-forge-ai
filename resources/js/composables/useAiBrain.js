export const AiBrainOutputTypes = {
    Text: 'Text',
    Image: 'Image',
}

export function buildAiBrainSearchUrl(outputTypeCode) {
    if (!outputTypeCode) {
        return route('search.ai-brains')
    }

    return route('search.ai-brains', { ai_brain_output_type_code: outputTypeCode })
}
