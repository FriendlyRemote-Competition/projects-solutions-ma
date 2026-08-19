import React, { useContext } from 'react';
import { AppContext } from '../context/AppContext';

const SettingsPanel = () => {
    const { 
        theme, 
        setTheme, 
        fontSize, 
        setFontSize, 
        lineSpacing, 
        setLineSpacing 
    } = useContext(AppContext);

    return (
        <div className="container py-3" style={{ maxWidth: '600px' }}>
            <div className="custom-card p-4">
                <h3 className="mb-4 text-neon-turquoise">Reading Settings</h3>

                <div className="mb-4">
                    <label className="text-muted-custom fw-bold mb-2 d-block">COLOR THEME</label>
                    <div className="d-flex gap-2">
                        <button
                            className={`btn btn-custom flex-grow-1 ${theme === 'dark' ? 'active' : ''}`}
                            onClick={() => setTheme('dark')}
                        >
                            Dark Mode
                        </button>
                        <button
                            className={`btn btn-custom flex-grow-1 ${theme === 'light' ? 'active' : ''}`}
                            onClick={() => setTheme('light')}
                        >
                            Light Mode
                        </button>
                    </div>
                </div>

                <div className="mb-4">
                    <label className="text-muted-custom fw-bold mb-2 d-block">FONT SIZE</label>
                    <div className="d-flex gap-2">
                        <button
                            className={`btn btn-custom flex-grow-1 ${fontSize === '15px' ? 'active' : ''}`}
                            onClick={() => setFontSize('15px')}
                        >
                            Small
                        </button>
                        <button
                            className={`btn btn-custom flex-grow-1 ${fontSize === '18px' ? 'active' : ''}`}
                            onClick={() => setFontSize('18px')}
                        >
                            Medium
                        </button>
                        <button
                            className={`btn btn-custom flex-grow-1 ${fontSize === '22px' ? 'active' : ''}`}
                            onClick={() => setFontSize('22px')}
                        >
                            Large
                        </button>
                    </div>
                </div>

                <div className="mb-3">
                    <label className="text-muted-custom fw-bold mb-2 d-block">LINE SPACING</label>
                    <div className="d-flex gap-2">
                        <button
                            className={`btn btn-custom flex-grow-1 ${lineSpacing === '1.4' ? 'active' : ''}`}
                            onClick={() => setLineSpacing('1.4')}
                        >
                            Compact
                        </button>
                        <button
                            className={`btn btn-custom flex-grow-1 ${lineSpacing === '1.8' ? 'active' : ''}`}
                            onClick={() => setLineSpacing('1.8')}
                        >
                            Standard
                        </button>
                        <button
                            className={`btn btn-custom flex-grow-1 ${lineSpacing === '2.2' ? 'active' : ''}`}
                            onClick={() => setLineSpacing('2.2')}
                        >
                            Loose
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default SettingsPanel;