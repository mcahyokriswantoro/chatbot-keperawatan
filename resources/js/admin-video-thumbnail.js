function frameScore(imageData) {
    const data = imageData.data;
    let sum = 0;
    let sumSq = 0;
    const pixels = data.length / 4;

    for (let i = 0; i < data.length; i += 4) {
        const lum = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
        sum += lum;
        sumSq += lum * lum;
    }

    const mean = sum / pixels;
    const variance = sumSq / pixels - mean * mean;

    if (mean < 18) {
        return -1;
    }

    const brightnessFactor = mean >= 28 && mean <= 225 ? 1 : 0.35;

    return variance * brightnessFactor;
}

function seekVideo(video, time) {
    return new Promise((resolve) => {
        const onSeeked = () => {
            video.removeEventListener('seeked', onSeeked);
            resolve();
        };

        video.addEventListener('seeked', onSeeked);
        video.currentTime = Math.min(Math.max(time, 0.05), Math.max(video.duration - 0.05, 0.05));
    });
}

export async function extractVideoThumbnails(videoSrc, count = 3) {
    const video = document.createElement('video');
    video.muted = true;
    video.playsInline = true;
    video.preload = 'auto';
    video.setAttribute('crossorigin', 'anonymous');

    await new Promise((resolve, reject) => {
        video.onloadedmetadata = () => resolve();
        video.onerror = () => reject(new Error('Video tidak dapat dibaca.'));
        video.src = videoSrc;
    });

    if (! video.duration || ! Number.isFinite(video.duration)) {
        throw new Error('Durasi video tidak valid.');
    }

    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const sampleRatios = [0.08, 0.2, 0.32, 0.44, 0.56, 0.68, 0.8, 0.92];
    const scored = [];

    for (const ratio of sampleRatios) {
        await seekVideo(video, ratio * video.duration);

        const width = video.videoWidth || 1280;
        const height = video.videoHeight || 720;
        canvas.width = width;
        canvas.height = height;
        ctx.drawImage(video, 0, 0, width, height);

        const imageData = ctx.getImageData(0, 0, width, height);
        const score = frameScore(imageData);

        if (score < 0) {
            continue;
        }

        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.88));

        if (! blob) {
            continue;
        }

        scored.push({
            score,
            ratio,
            blob,
            url: URL.createObjectURL(blob),
        });
    }

    video.removeAttribute('src');
    video.load();
    video.remove();

    if (scored.length === 0) {
        throw new Error('Tidak ada frame yang cocok untuk thumbnail.');
    }

    scored.sort((a, b) => b.score - a.score);

    const picked = [];
    const usedRatios = [];

    for (const frame of scored) {
        const tooClose = usedRatios.some((ratio) => Math.abs(ratio - frame.ratio) < 0.12);

        if (tooClose) {
            continue;
        }

        picked.push(frame);
        usedRatios.push(frame.ratio);

        if (picked.length >= count) {
            break;
        }
    }

    while (picked.length < count && scored.length > picked.length) {
        const next = scored.find((frame) => ! picked.includes(frame));

        if (! next) {
            break;
        }

        picked.push(next);
    }

    return picked.map((frame, index) => ({
        id: index,
        url: frame.url,
        blob: frame.blob,
        label: `Frame ${index + 1}`,
    }));
}

export function blobToCoverFile(blob, filename = 'video-thumbnail.jpg') {
    return new File([blob], filename, { type: blob.type || 'image/jpeg', lastModified: Date.now() });
}

export function assignCoverFile(input, file) {
    if (! input) {
        return;
    }

    const transfer = new DataTransfer();
    transfer.items.add(file);
    input.files = transfer.files;
}

document.addEventListener('alpine:init', () => {
    window.Alpine.data('adminArticleForm', (config = {}) => ({
        isVideo: Boolean(config.isVideo),
        preview: config.existingCover || null,
        existingVideoUrl: config.existingVideoUrl || null,
        videoObjectUrl: null,
        thumbOptions: [],
        selectedFrameIndex: null,
        thumbSource: null,
        uploadPreviewUrl: null,
        videoMode: config.videoMode || 'file',
        defaultVideoPath: config.defaultVideoPath || '',
        serverVideos: config.serverVideos || [],
        serverVideoPreviewUrl: null,
        extracting: false,
        thumbError: null,

        init() {
            if (this.isVideo && this.videoMode === 'file' && this.existingVideoUrl && ! this.preview) {
                this.generateThumbnails(this.existingVideoUrl);
            }

            if (this.isVideo && this.videoMode === 'server' && this.defaultVideoPath) {
                const match = this.serverVideos.find((item) => item.path === this.defaultVideoPath);

                if (match) {
                    this.serverVideoPreviewUrl = match.url;

                    if (! this.preview) {
                        this.generateThumbnails(match.url);
                    }
                }
            }
        },

        onCoverFileChange(event) {
            if (this.isVideo) {
                return;
            }

            const file = event.target.files[0];

            if (! file) {
                return;
            }

            this.preview = URL.createObjectURL(file);
        },

        onManualThumbUpload(event) {
            const file = event.target.files[0];

            if (! file) {
                return;
            }

            if (this.uploadPreviewUrl) {
                URL.revokeObjectURL(this.uploadPreviewUrl);
            }

            this.uploadPreviewUrl = URL.createObjectURL(file);
            this.preview = this.uploadPreviewUrl;
            this.thumbSource = 'upload';
            this.selectedFrameIndex = null;
            this.thumbError = null;
            assignCoverFile(this.$refs.coverInput, file);
        },

        async onServerVideoChange(event) {
            const option = event.target.selectedOptions[0];
            const url = option?.dataset?.url || null;

            this.clearThumbOptions();
            this.serverVideoPreviewUrl = url;

            if (! url) {
                return;
            }

            await this.generateThumbnails(url);
        },

        async onVideoFileChange(event) {
            const file = event.target.files[0];

            this.clearThumbOptions();

            if (! file) {
                if (this.videoObjectUrl) {
                    URL.revokeObjectURL(this.videoObjectUrl);
                    this.videoObjectUrl = null;
                }

                if (this.existingVideoUrl) {
                    await this.generateThumbnails(this.existingVideoUrl);
                }

                return;
            }

            if (this.videoObjectUrl) {
                URL.revokeObjectURL(this.videoObjectUrl);
            }

            this.videoObjectUrl = URL.createObjectURL(file);
            await this.generateThumbnails(this.videoObjectUrl);
        },

        async generateThumbnails(source) {
            this.extracting = true;
            this.thumbError = null;

            try {
                const options = await extractVideoThumbnails(source, 3);
                this.thumbOptions = options;

                if (options.length > 0 && this.thumbSource !== 'upload') {
                    this.selectFrame(0);
                }
            } catch (error) {
                this.thumbError = error?.message || 'Gagal mengambil thumbnail dari video.';
                if (this.thumbSource !== 'upload') {
                    this.preview = null;
                }
                this.selectedFrameIndex = null;
            } finally {
                this.extracting = false;
            }
        },

        selectFrame(index) {
            const option = this.thumbOptions[index];

            if (! option) {
                return;
            }

            this.thumbSource = 'frame';
            this.selectedFrameIndex = index;
            this.preview = option.url;
            this.thumbError = null;

            if (this.$refs.manualThumbInput) {
                this.$refs.manualThumbInput.value = '';
            }

            assignCoverFile(this.$refs.coverInput, blobToCoverFile(option.blob));
        },

        clearThumbOptions() {
            this.thumbOptions.forEach((option) => URL.revokeObjectURL(option.url));
            this.thumbOptions = [];
            this.selectedFrameIndex = null;
            this.thumbError = null;

            if (this.thumbSource === 'frame') {
                this.thumbSource = this.uploadPreviewUrl ? 'upload' : null;
                this.preview = this.uploadPreviewUrl || null;
            }

            const input = this.$refs.coverInput;

            if (input && this.thumbSource !== 'upload') {
                input.value = '';
            }
        },

        clearManualUpload() {
            const wasUpload = this.thumbSource === 'upload';

            if (this.uploadPreviewUrl) {
                URL.revokeObjectURL(this.uploadPreviewUrl);
                this.uploadPreviewUrl = null;
            }

            if (wasUpload) {
                this.preview = null;
                this.thumbSource = null;
            }

            if (this.$refs.manualThumbInput) {
                this.$refs.manualThumbInput.value = '';
            }

            if (this.$refs.coverInput && wasUpload) {
                this.$refs.coverInput.value = '';
            }
        },

        onSubmit(event) {
            if (! this.isVideo) {
                return;
            }

            if (this.videoMode === 'link') {
                return;
            }

            if (! this.preview) {
                event.preventDefault();
                this.thumbError = 'Pilih thumbnail dari video atau unggah gambar thumbnail.';
            }
        },
    }));
});
