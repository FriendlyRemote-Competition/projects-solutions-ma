import React, { useContext } from 'react';
import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import { AppContext } from './context/AppContext';
import Library from './components/Library';
import Chapter from './components/Chapter';
import Bookmarks from './components/Bookmarks';
import SearchPanel from './components/SearchPanel';
import SettingsPanel from './components/SettingsPanel';

function App() {
  const { theme } = useContext(AppContext);

  return (
    <div className={`app-container theme-${theme}`}>
      <Router>
        <nav className="navbar custom-navbar px-4 py-3">
          <Link className="navbar-brand fw-bold text-neon-violet" to="/">
            Interactive E-Book
          </Link>
          <div className="d-flex gap-2">
            <Link to="/search" className="btn btn-custom btn-sm rounded-pill px-3">Search</Link>
            <Link to="/bookmarks" className="btn btn-custom btn-sm rounded-pill px-3">Bookmarks</Link>
            <Link to="/settings" className="btn btn-custom btn-sm rounded-pill px-3">Settings</Link>
          </div>
        </nav>

        <main className="p-4 container-fluid">
          <Routes>
            <Route path="/" element={<Library />} />
            <Route path="/chapter/:chapterId" element={<Chapter />} />
            <Route path="/bookmarks" element={<Bookmarks />} />
            <Route path="/search" element={<SearchPanel />} />
            <Route path="/settings" element={<SettingsPanel />} />
          </Routes>
        </main>
      </Router>
    </div>
  );
}

export default App;