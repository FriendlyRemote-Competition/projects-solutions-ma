import React, { useState, useContext } from 'react';
import { AppContext } from '../context/AppContext';
import { Link } from 'react-router-dom';

const SearchPanel = () => {
    const { data } = useContext(AppContext);
    const [query, setQuery] = useState('');

    const searchTerm = query.trim().toLowerCase();

    const results = [];
    if (searchTerm.length > 0 && data?.chapters) {
        data.chapters.forEach((chapter) => {
            chapter.sections?.forEach((section) => {
                const headingMatch = section.heading?.toLowerCase().includes(searchTerm);
                const contentMatch = section.content?.toLowerCase().includes(searchTerm);

                if (headingMatch || contentMatch) {
                    let snippet = section.content || '';
                    if (contentMatch) {
                        const index = snippet.toLowerCase().indexOf(searchTerm);
                        const start = Math.max(0, index - 40);
                        const end = Math.min(snippet.length, index + 100);
                        snippet = (start > 0 ? '...' : '') + snippet.slice(start, end) + (end < snippet.length ? '...' : '');
                    } else {
                        snippet = snippet.substring(0, 120) + '...';
                    }

                    results.push({
                        chapterId: chapter.id,
                        chapterNum: chapter.number,
                        sectionId: section.id,
                        heading: section.heading,
                        snippet,
                    });
                }
            });
        });
    }

    return (
        <div className="container py-3" style={{ maxWidth: '800px' }}>
            <div className="custom-card p-4 mb-4">
                <h3 className="mb-3 text-neon-turquoise fw-bold">Search the Textbook</h3>
                <input
                    type="text"
                    className="form-control custom-input p-3 fs-5"
                    placeholder="Search headings, terms, or content..."
                    value={query}
                    onChange={(e) => setQuery(e.target.value)}
                    autoFocus
                />
            </div>

            <div className="results-list">
                {query.trim().length > 0 && results.length === 0 && (
                    <div className="custom-card p-4 text-center">
                        <p className="text-muted-custom mb-0 fs-5">No matching results found for "{query}".</p>
                    </div>
                )}

                {results.map((res, index) => (
                    <div key={`${res.chapterId}-${res.sectionId}-${index}`} className="chapter-card p-4 mb-3">
                        <Link to={`/chapter/${res.chapterId}`} className="text-decoration-none">
                            <div className="d-flex justify-content-between align-items-center mb-2">
                                <h5 className="text-neon-violet mb-0">
                                    Chapter {res.chapterNum}: {res.heading}
                                </h5>
                                <span className="btn btn-custom btn-sm rounded-pill">Read →</span>
                            </div>
                            <p className="text-main mb-0 fs-6">{res.snippet}</p>
                        </Link>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default SearchPanel;