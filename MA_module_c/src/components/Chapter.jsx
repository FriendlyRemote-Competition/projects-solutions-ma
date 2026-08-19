import React, { useContext, useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { AppContext } from '../context/AppContext';

const assetImages = import.meta.glob('../assets/images/*', { eager: true });

// here i get all the correct urls for the images in the assets/images folder, and then i can use them in the component.
// Sorry for the orthography mistakes
const getImageUrl = (imagePath) => {
    if (!imagePath) return '';
    if (imagePath.startsWith('http')) return imagePath;

    const fileName = imagePath.split('/').pop();
    const key = `../assets/images/${fileName}`; // Construct the key based on the file name

    return assetImages[key]?.default || ''; // Return the URL if found, otherwise return an empty string
};

const Chapter = () => {
    const { chapterId } = useParams();
    const {
        data,
        bookmarks,
        toggleBookmark,
        fontSize,
        lineSpacing,
        markSectionRead
    } = useContext(AppContext);

    const chapter = data.chapters.find(c => c.id === chapterId);
    const [currentSectionIndex, setCurrentSectionIndex] = useState(0);

    useEffect(() => {
        if (chapter && chapter.sections[currentSectionIndex]) {
            markSectionRead(chapter.id, chapter.sections[currentSectionIndex].id);
        }
    }, [chapterId, currentSectionIndex, chapter]);

    if (!chapter) {
        return <div className="text-center mt-5 text-danger">Chapter not found.</div>;
    }

    const section = chapter.sections[currentSectionIndex];
    const isBookmarked = bookmarks.some(b => b.sectionId === section.id);
    const ImageUrl = getImageUrl(section.image);

    const progress = ((currentSectionIndex + 1) / chapter.sections.length) * 100;

    return (
        <div className="row g-4">
            {/* Sidebar Navigation */}
            <div className="col-md-3">
                <div className="custom-card p-3">
                    <Link to="/" className="text-decoration-none text-muted-custom mb-3 d-block">← Back to Library</Link>
                    <h6 className="text-neon-turquoise mb-3 fw-bold">Sections</h6>
                    <div className="d-flex flex-column gap-1">
                        {chapter.sections.map((sec, index) => (
                            <button
                                key={sec.id}
                                className={`btn text-start btn-sm p-2 ${index === currentSectionIndex ? 'btn-custom active' : 'btn-custom'}`}
                                onClick={() => setCurrentSectionIndex(index)}
                            >
                                {index + 1}. {sec.heading}
                            </button>
                        ))}
                    </div>
                </div>
            </div>

            {/* Reader Content */}
            <div className="col-md-9">
                <div className="custom-card p-4">
                    <div className="mb-4 pb-3 border-bottom border-secondary">
                        <div className="d-flex justify-content-between align-items-center mb-3">
                            <span className="text-muted-custom">
                                Chapter {chapter.number} — Section {currentSectionIndex + 1} of {chapter.sections.length}
                            </span>

                            <button
                                className={`btn btn-sm ${isBookmarked ? 'btn-warning' : 'btn-custom'}`}
                                onClick={() => toggleBookmark(chapter.id, section.id, section.heading, section.content)}
                            >
                                {isBookmarked ? '★ Bookmarked' : '☆ Bookmark'}
                            </button>
                        </div>

                        <div className="d-flex justify-content-between align-items-center mb-1">
                            <small className="text-muted-custom">
                                Chapter Progress
                            </small>
                            <small className="text-neon-turquoise fw-bold">
                                {Math.round(progress)}%
                            </small>
                        </div>

                        <div
                            className="progress"
                            style={{
                                height: '8px',
                                backgroundColor: '#94a3b833',
                                borderRadius: '10px',
                                overflow: 'hidden'
                            }}
                        >
                            <div
                                className="progress-bar"
                                role="progressbar"
                                style={{
                                    width: `${progress}%`,
                                    transition: 'width 0.3s ease',
                                    backgroundColor: '#7c3aed'
                                }}
                                aria-valuenow={progress}
                                aria-valuemin="0"
                                aria-valuemax="100"
                            />
                        </div>
                    </div>

                    <h2 className="mb-4 text-neon-violet">{section.heading}</h2>

                    <div
                        className="reader-content my-4"
                        style={{ fontSize: fontSize, lineHeight: lineSpacing }}
                    >
                        {section.content}
                    </div>

                    {/* Render Image  */}
                    {ImageUrl && (
                        <div className="my-4 text-center">
                            <img
                                src={ImageUrl}
                                alt={section.imageAlt || 'Section illustration'}
                                className="img-fluid rounded border border-secondary shadow-sm"
                                style={{ maxHeight: '420px', objectFit: 'contain' }}
                            />
                        </div>
                    )}

                    <div className="d-flex justify-content-between pt-4 mt-4 border-top border-secondary">
                        <button
                            className="btn btn-custom"
                            onClick={() => setCurrentSectionIndex(prev => prev - 1)}
                            disabled={currentSectionIndex === 0}
                        >
                            ← Previous Section
                        </button>
                        <button
                            className="btn btn-custom"
                            onClick={() => setCurrentSectionIndex(prev => prev + 1)}
                            disabled={currentSectionIndex === chapter.sections.length - 1}
                        >
                            Next Section →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Chapter;