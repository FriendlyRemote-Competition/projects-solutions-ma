import React, { useContext } from 'react';
import { AppContext } from '../context/AppContext';
import { Link } from 'react-router-dom';

const Bookmarks = () => {
    const { bookmarks, removeBookmark } = useContext(AppContext);

    return (
        <div className="container py-4">
            <h2 className="mb-4 text-warning">My Bookmarks ({bookmarks.length})</h2>

            {bookmarks.length === 0 ? (
                <div className="text-center py-5">
                    <p className="text-muted fs-4">You don't have any bookmarks yet.</p>
                </div>
            ) : (
                <div className="row gap-3">
                    {bookmarks.map((bm) => (
                        <div key={bm.sectionId} className="col-12 chapter-card p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 className="text-light">{bm.heading}</h5>
                                <p className="text-muted fst-italic mb-0">"{bm.excerpt.substring(0, 80)}..."</p>
                            </div>
                            <div className="d-flex gap-2">
                                <Link to={`/chapter/${bm.chapterId}`} className="btn btn-sm btn-outline-info">Go to</Link>
                                <button onClick={() => removeBookmark(bm.sectionId)} className="btn btn-sm btn-outline-danger">Remove</button>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
};

export default Bookmarks;