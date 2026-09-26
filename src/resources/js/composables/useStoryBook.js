export const statuses = {
    Draft: "Draft",
    ProcessingText: "Processing Text",
    StopText: "Stop Text",
    CompleteText: "Complete Text",
    ProcessingIllustration: "Processing Illustration",
    StopIllustration: "Stop Illustration",
    Complete: "Complete",
}

export const textGenerationStepCount = 15;

export const illustrationGenerationStep = 16;

export const statusLabels = {
    [statuses.Draft]: "Draft",
    [statuses.ProcessingText]: "Processing Text",
    [statuses.StopText]: "Stop Text",
    [statuses.CompleteText]: "Complete Text",
    [statuses.ProcessingIllustration]: "Processing Illustration",
    [statuses.StopIllustration]: "Stop Illustration",
    [statuses.Complete]: "Complete",
}

export const statusClasses = {
    [statuses.Draft]: "bg-gray-100 text-gray-700",
    [statuses.ProcessingText]: "bg-blue-100 text-blue-700",
    [statuses.StopText]: "bg-amber-100 text-amber-700",
    [statuses.CompleteText]: "bg-green-100 text-green-700",
    [statuses.ProcessingIllustration]: "bg-purple-100 text-purple-700",
    [statuses.StopIllustration]: "bg-amber-100 text-amber-700",
    [statuses.Complete]: "bg-emerald-100 text-emerald-700",
}

export const isTextGenerationRunning = (status) =>
    status === statuses.ProcessingText;

export const isIllustrationGenerationRunning = (status) =>
    status === statuses.ProcessingIllustration;

export const isGenerationRunning = (status) =>
    isTextGenerationRunning(status) || isIllustrationGenerationRunning(status);

export const isTextGenerationComplete = (status) =>
    status === statuses.CompleteText;

export const isTextGenerationStopped = (status) => status === statuses.StopText;

export const isIllustrationGenerationStopped = (status) =>
    status === statuses.StopIllustration;

export const isStoryBookComplete = (status) => status === statuses.Complete;

export const isLocked = (status) => isStoryBookComplete(status);

export const canStartIllustrationGeneration = (status) =>
    isTextGenerationComplete(status);

export const canResumeTextGeneration = (status) =>
    status === statuses.StopText || status === statuses.Draft;
