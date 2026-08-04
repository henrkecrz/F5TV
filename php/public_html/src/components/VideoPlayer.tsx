/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useRef, useState, useEffect } from 'react';
import { 
  Play, Pause, RotateCcw, RotateCw, Volume2, VolumeX, 
  Maximize, Minimize, Square, ChevronRight, X 
} from 'lucide-react';

interface VideoPlayerProps {
  videoUrl: string;
  title: string;
  subtitle?: string; // e.g., "Temporada 1 - Episódio 1"
  posterUrl?: string;
  onClose: () => void;
  onNextEpisode?: () => void;
  hasNextEpisode?: boolean;
}

export default function VideoPlayer({ 
  videoUrl, 
  title, 
  subtitle, 
  posterUrl,
  onClose, 
  onNextEpisode, 
  hasNextEpisode = false 
}: VideoPlayerProps) {
  const videoRef = useRef<HTMLVideoElement>(null);
  const containerRef = useRef<HTMLDivElement>(null);

  const [isPlaying, setIsPlaying] = useState(false);
  const [progress, setProgress] = useState(0);
  const [duration, setDuration] = useState(0);
  const [currentTime, setCurrentTime] = useState(0);
  const [volume, setVolume] = useState(0.8);
  const [isMuted, setIsMuted] = useState(false);
  const [isFullscreen, setIsFullscreen] = useState(false);
  const [isControlsVisible, setIsControlsVisible] = useState(true);

  // Auto hide controls after inactivity
  useEffect(() => {
    let timeoutId: number;
    const handleMouseMove = () => {
      setIsControlsVisible(true);
      clearTimeout(timeoutId);
      timeoutId = window.setTimeout(() => {
        if (isPlaying) {
          setIsControlsVisible(false);
        }
      }, 3000);
    };

    const container = containerRef.current;
    if (container) {
      container.addEventListener('mousemove', handleMouseMove);
    }

    return () => {
      if (container) {
        container.removeEventListener('mousemove', handleMouseMove);
      }
      clearTimeout(timeoutId);
    };
  }, [isPlaying]);

  const handlePlayPause = () => {
    if (videoRef.current) {
      if (isPlaying) {
        videoRef.current.pause();
      } else {
        videoRef.current.play().catch(err => console.log('Autoplay blocked or playback error: ', err));
      }
      setIsPlaying(!isPlaying);
    }
  };

  const handleTimeUpdate = () => {
    if (videoRef.current) {
      const current = videoRef.current.currentTime;
      const total = videoRef.current.duration || 0;
      setCurrentTime(current);
      if (total > 0) {
        setProgress((current / total) * 100);
      }
    }
  };

  const handleLoadedMetadata = () => {
    if (videoRef.current) {
      setDuration(videoRef.current.duration || 0);
    }
  };

  const handleSkip = (seconds: number) => {
    if (videoRef.current) {
      videoRef.current.currentTime = Math.max(0, Math.min(videoRef.current.currentTime + seconds, duration));
    }
  };

  const handleVolumeChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const val = parseFloat(e.target.value);
    setVolume(val);
    if (videoRef.current) {
      videoRef.current.volume = val;
      videoRef.current.muted = val === 0;
    }
    setIsMuted(val === 0);
  };

  const toggleMute = () => {
    if (videoRef.current) {
      const nextMute = !isMuted;
      setIsMuted(nextMute);
      videoRef.current.muted = nextMute;
      if (!nextMute && volume === 0) {
        setVolume(0.5);
        videoRef.current.volume = 0.5;
      }
    }
  };

  const handleScrub = (e: React.ChangeEvent<HTMLInputElement>) => {
    const pct = parseFloat(e.target.value);
    setProgress(pct);
    if (videoRef.current && duration > 0) {
      videoRef.current.currentTime = (pct / 100) * duration;
    }
  };

  const toggleFullscreen = () => {
    if (!containerRef.current) return;

    if (!document.fullscreenElement) {
      containerRef.current.requestFullscreen().catch((err) => {
        console.error('Fullscreen request rejected', err);
      });
      setIsFullscreen(true);
    } else {
      document.exitFullscreen();
      setIsFullscreen(false);
    }
  };

  useEffect(() => {
    const handleFullscreenChange = () => {
      setIsFullscreen(!!document.fullscreenElement);
    };
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    return () => document.removeEventListener('fullscreenchange', handleFullscreenChange);
  }, []);

  // Format time utility (e.g. 01:23)
  const formatTime = (seconds: number) => {
    if (isNaN(seconds)) return '00:00';
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  };

  return (
    <div 
      id="f5-video-player-container"
      ref={containerRef}
      className="fixed inset-0 bg-black z-50 flex items-center justify-center select-none overflow-hidden text-white font-sans"
    >
      {/* Video Element */}
      {posterUrl && (
        <div
          className="absolute inset-0 bg-cover bg-center opacity-25 blur-sm scale-105"
          style={{ backgroundImage: `url(${posterUrl})` }}
        />
      )}
      <div className="absolute inset-0 bg-black/35" />
      <video
        id="f5-html5-video"
        ref={videoRef}
        src={videoUrl || "https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4"}
        poster={posterUrl}
        className="w-full h-full object-contain cursor-pointer"
        onClick={handlePlayPause}
        onTimeUpdate={handleTimeUpdate}
        onLoadedMetadata={handleLoadedMetadata}
        autoPlay
        playsInline
        onPlay={() => setIsPlaying(true)}
        onPause={() => setIsPlaying(false)}
      />

      {/* Top Bar Overlay */}
      <div 
        className={`absolute top-0 inset-x-0 p-6 bg-gradient-to-b from-black/80 to-transparent flex items-center justify-between transition-opacity duration-300 ${
          isControlsVisible ? 'opacity-100' : 'opacity-0 pointer-events-none'
        }`}
      >
        <div className="flex flex-col">
          <span className="text-sm font-mono tracking-widest text-[#ef4444] font-semibold uppercase">F5 TV PLAYER</span>
          <h2 className="text-xl md:text-2xl font-bold tracking-tight">{title}</h2>
          {subtitle && <p className="text-gray-400 text-sm mt-0.5">{subtitle}</p>}
        </div>

        <button 
          id="btn-close-player"
          onClick={onClose}
          className="p-3 bg-zinc-900/80 hover:bg-zinc-800 border border-zinc-800 rounded-full transition cursor-pointer text-gray-300 hover:text-white"
          title="Fechar player"
        >
          <X className="w-6 h-6" />
        </button>
      </div>

      {/* Double tap / Large Play Pause Center Action Indicating Status */}
      {!isPlaying && (
        <button 
          id="btn-center-play"
          onClick={handlePlayPause}
          className="absolute p-6 bg-red-600/90 hover:bg-red-600/100 rounded-full shadow-lg shadow-black/60 transform transition duration-200 active:scale-90 flex items-center justify-center cursor-pointer"
        >
          <Play className="w-10 h-10 text-white fill-white ml-1" />
        </button>
      )}

      {/* Bottom Controls Overlay */}
      <div 
        className={`absolute bottom-0 inset-x-0 p-6 bg-gradient-to-t from-black/95 via-black/70 to-transparent transition-opacity duration-300 flex flex-col gap-4 ${
          isControlsVisible ? 'opacity-100' : 'opacity-0 pointer-events-none'
        }`}
      >
        {/* Progress Slider */}
        <div className="flex items-center gap-4">
          <span className="text-xs font-mono text-gray-300">{formatTime(currentTime)}</span>
          <input
            id="timeline-scrubber"
            type="range"
            min="0"
            max="100"
            step="0.1"
            value={progress}
            onChange={handleScrub}
            className="flex-1 h-1 bg-zinc-700 rounded-lg appearance-none cursor-pointer accent-[#ef4444]"
          />
          <span className="text-xs font-mono text-gray-300">{formatTime(duration)}</span>
        </div>

        {/* Buttons Control Panel */}
        <div className="flex flex-wrap items-center justify-between gap-4">
          <div className="flex items-center gap-4">
            {/* Play/Pause */}
            <button 
              id="player-play-btn"
              onClick={handlePlayPause} 
              className="p-2 cursor-pointer text-gray-200 hover:text-white hover:scale-105 transition"
              title={isPlaying ? "Pausar (Espaço)" : "Reproduzir (Espaço)"}
            >
              {isPlaying ? <Pause className="w-5 h-5 fill-current" /> : <Play className="w-5 h-5 fill-current" />}
            </button>

            {/* Back 10s */}
            <button 
              id="player-back-10s"
              onClick={() => handleSkip(-10)} 
              className="p-2 cursor-pointer text-gray-300 hover:text-white hover:scale-105 transition"
              title="Voltar 10 segundos"
            >
              <RotateCcw className="w-5 h-5" />
            </button>

            {/* Forward 10s */}
            <button 
              id="player-forward-10s"
              onClick={() => handleSkip(10)} 
              className="p-2 cursor-pointer text-gray-300 hover:text-white hover:scale-105 transition"
              title="Avançar 10 segundos"
            >
              <RotateCw className="w-5 h-5" />
            </button>

            {/* Divider */}
            <span className="h-4 w-[1px] bg-zinc-700" />

            {/* Volume Control */}
            <div className="flex items-center gap-2">
              <button 
                id="player-mute-btn"
                onClick={toggleMute} 
                className="p-2 cursor-pointer text-gray-300 hover:text-white transition"
              >
                {isMuted ? <VolumeX className="w-5 h-5" /> : <Volume2 className="w-5 h-5" />}
              </button>
              <input
                id="volume-slider"
                type="range"
                min="0"
                max="1"
                step="0.05"
                value={isMuted ? 0 : volume}
                onChange={handleVolumeChange}
                className="w-16 md:w-24 h-1 bg-zinc-700 rounded-lg appearance-none cursor-pointer accent-white"
              />
            </div>
          </div>

          <div className="flex items-center gap-3">
            {/* Next Episode Trigger if applicable */}
            {hasNextEpisode && onNextEpisode && (
              <button
                id="player-next-episode-btn"
                onClick={onNextEpisode}
                className="flex items-center gap-1 bg-zinc-800/80 hover:bg-[#ef4444] border border-zinc-700/60 hover:border-transparent text-xs md:text-sm px-3 py-1.5 rounded-lg font-medium transition cursor-pointer"
                title="Próximo Episódio"
              >
                <span>Próximo Episódio</span>
                <ChevronRight className="w-4 h-4" />
              </button>
            )}

            {/* Fullscreen Toggle */}
            <button 
              id="player-fullscreen-btn"
              onClick={toggleFullscreen} 
              className="p-2 cursor-pointer text-gray-300 hover:text-white transition"
              title={isFullscreen ? "Sair da Tela Cheia" : "Tela Cheia"}
            >
              {isFullscreen ? <Minimize className="w-5 h-5" /> : <Maximize className="w-5 h-5" />}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
