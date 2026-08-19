import React, { useContext } from 'react';
import { AppContext } from '../context/AppContext';
import { Link } from 'react-router-dom';

const Library = () => {
    const { data, overallProgress, getChapterProgress } = useContext(AppContext);

    return (
        <div className="container py-4">
            <header className="mb-4">
                <h1 className="fw-bold text-neon-violet">{data.book?.title || 'Interactive E-Book'}</h1>
                <p className="text-muted-custom mb-0">{data.book?.subtitle || 'Course Reader & Interactive Guide'}</p>
            </header>

            <div className="custom-card p-4 mb-5">
                <div className="d-flex justify-content-between align-items-center mb-2">
                    <span className="fw-bold text-neon-turquoise">Overall Progress</span>
                    <span className="fw-bold text-neon-turquoise">{overallProgress}%</span>
                </div>
                <div className="custom-progress">
                    <div
                        className="custom-progress-bar"
                        style={{ width: `${overallProgress}%` }}
                    ></div>
                </div>
            </div>

            <h4 className="mb-3 text-neon-turquoise">Chapters</h4>
            <div className="d-flex flex-column gap-3">
                {data.chapters.map((chapter) => {
                    const progressPercent = getChapterProgress(chapter);
                    return (
                        <Link to={`/chapter/${chapter.id}`} className="text-decoration-none" key={chapter.id}>
                            <div className="chapter-card p-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 className="mb-1 text-main">
                                        Chapter {chapter.number}: {chapter.title}
                                    </h5>
                                    <p className="text-muted-custom mb-0 fs-7">
                                        {chapter.sections.length} section{chapter.sections.length > 1 ? 's' : ''}
                                    </p>
                                </div>
                                <div className="text-end">
                                    <span className="btn btn-custom btn-sm rounded-pill px-3">
                                        {progressPercent > 0 ? `${progressPercent}% completed` : 'Not started'} →
                                    </span>
                                </div>
                            </div>
                        </Link>
                    );
                })}
            </div>
        </div>
    );
};

export default Library;