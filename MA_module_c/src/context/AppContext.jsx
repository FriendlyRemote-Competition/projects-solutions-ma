import React, { createContext, useState, useEffect } from 'react';
import textbookData from '../data.json';

export const AppContext = createContext();

export const AppProvider = ({ children }) => {
    const [readSectionsMap, setReadSectionsMap] = useState(() =>
        JSON.parse(localStorage.getItem('readSectionsMap')) || {}
    );
    const [bookmarks, setBookmarks] = useState(() =>
        JSON.parse(localStorage.getItem('bookmarks')) || []
    );
    const [theme, setTheme] = useState(() =>
        localStorage.getItem('theme') || 'dark'
    );
    const [fontSize, setFontSize] = useState(() =>
        localStorage.getItem('fontSize') || '18px'
    );
    const [lineSpacing, setLineSpacing] = useState(() =>
        localStorage.getItem('lineSpacing') || '1.8'
    );

    useEffect(() => {
        localStorage.setItem('readSectionsMap', JSON.stringify(readSectionsMap));
    }, [readSectionsMap]);
    useEffect(() => {
        localStorage.setItem('bookmarks', JSON.stringify(bookmarks));
    }, [bookmarks]);
    useEffect(() => {
        localStorage.setItem('theme', theme);
    }, [theme]);
    useEffect(() => {
        localStorage.setItem('fontSize', fontSize);
    }, [fontSize]);
    useEffect(() => {
        localStorage.setItem('lineSpacing', lineSpacing);
    }, [lineSpacing]);

    const markSectionRead = (chapterId, sectionId) => {
        setReadSectionsMap(prev => ({ ...prev, [sectionId]: true }));
    };

    const getChapterProgress = (chapter) => {
        if (!chapter || !chapter.sections || chapter.sections.length === 0) return 0;
        const readCount = chapter.sections.filter(sec => readSectionsMap[sec.id]).length;
        return Math.round((readCount / chapter.sections.length) * 100);
    };

    const toggleBookmark = (chapterId, sectionId, heading, excerpt) => {
        setBookmarks(prev => {
            const exists = prev.some(b => b.sectionId === sectionId);
            if (exists) {
                return prev.filter(b => b.sectionId !== sectionId);
            }
            return [...prev, { chapterId, sectionId, heading, excerpt }];
        });
    };

    const removeBookmark = (sectionId) => {
        setBookmarks(prev => prev.filter(b => b.sectionId !== sectionId));
    };

    const calculateOverallProgress = () => {
        let totalSections = 0;
        textbookData.chapters.forEach(ch => { totalSections += ch.sections.length; });
        const readCount = Object.keys(readSectionsMap).length;
        return totalSections > 0 ? Math.round((readCount / totalSections) * 100) : 0;
    };

    return (
        <AppContext.Provider
            value={{
                data: textbookData,
                bookmarks,
                toggleBookmark,
                removeBookmark,
                theme,
                setTheme,
                fontSize,
                setFontSize,
                lineSpacing,
                setLineSpacing,
                markSectionRead,
                getChapterProgress,
                overallProgress: calculateOverallProgress(),
            }}
        >
            {children}
        </AppContext.Provider>
    );
};