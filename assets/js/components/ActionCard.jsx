import React from 'react';

export default function ActionCard({ icon, title, description, onClick }) {
  return (
    <button className="aistrix-action-card" onClick={onClick}>
      <span className="aistrix-action-card__icon">{icon}</span>
      <div className="aistrix-action-card__content">
        <div className="aistrix-action-card__title">{title}</div>
        <div className="aistrix-action-card__desc">{description}</div>
      </div>
    </button>
  );
}